<?php

namespace App\DataTables\Duel;

use App\Models\Duel\DuelRelease;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;
class DuelReleaseDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('quantity_total', function ($row) {
                return number_format($row->running_total ?? 0) . ' L';
            })
            ->editColumn('quantity_request', function ($row) {
                return number_format($row->quantity_request ?? 0) . ' L';
            })
            ->editColumn('duel_total', function ($row) {
                $remain = ($row->running_total ?? 0) - ($row->quantity_request ?? 0);
                return number_format(max(0, $remain)) . ' L';
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                return is_null($soft_delete->deleted_at) 
                    ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' 
                    : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
            })
            ->editColumn('item_name', function ($row) {
                return $row->item_name ?? '-';
            })
            ->addColumn('action', function ($module) {
                return view('duel::duelRelease.action', ['module' => $module]);
            })
            ->editColumn('file', function ($row) {
                if (!$row->file) {
                    return '<span class="text-muted">-</span>';
                }
                $files = json_decode($row->file, true);
                if (is_array($files)) {
                    $html = '<ul class="list-unstyled m-0">';
                    foreach ($files as $file) {
                        $url = asset('storage/uploads/' . $file);
                        $html .= "<li><a href='$url' target='_blank' class='text-primary'><i class='fas fa-file-alt me-1'></i>$file</a></li>";
                    }
                    $html .= '</ul>';
                    return $html;
                }
                $url = asset('storage/uploads/' . $row->file);
                return "<a href='$url' target='_blank' class='text-primary'><i class='fas fa-file-alt me-1'></i>Preview</a>";
            })
            ->rawColumns(['note', 'refer', 'file', 'soft_delete']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DuelRelease $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $ministryId = decode_params($params);

        $query = $model->newQuery()
            ->leftJoin('agencies', 'duel_releases.agency', '=', 'agencies.id')
            ->leftJoin('duel_types', 'duel_releases.item_name', '=', 'duel_types.id')
            ->leftJoin('duel_entries', function ($join) use ($ministryId) {
                $join->on('duel_entries.stock_number', '=', 'duel_releases.stock_number')
                    ->on('duel_entries.item_name', '=', 'duel_releases.item_name')
                    ->where('duel_entries.ministry_id', '=', $ministryId);
            })
            ->where('duel_releases.ministry_id', $ministryId);

        // Filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('duel_releases.date_release', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('duel_releases.date_release', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('duel_releases.date_release', '<=', $request->end_date);
        }

        if ($request->filled('cboNumber')) {
            $query->where('duel_releases.receipt_number', $request->cboNumber);
        }
        if ($request->filled('cboUserRequest')) {
            $query->where('duel_releases.user_request', $request->cboUserRequest);
        }
        if ($request->filled('cboDuelType')) {
            $query->where('duel_releases.item_name', $request->cboDuelType);
        }
        if ($request->filled('cboExecutiveUnit')) {
            $query->where('duel_releases.executive_unit', $request->cboExecutiveUnit);
        }

        // Subquery matching release sequence strictly by (date_release, receipt_number, id)
        $previousReleasesSubQuery =DB::table('duel_releases as dr_prev')
            ->selectRaw('COALESCE(SUM(dr_prev.quantity_request), 0)')
            ->whereColumn('dr_prev.ministry_id', 'duel_releases.ministry_id')
            ->whereColumn('dr_prev.stock_number', 'duel_releases.stock_number')
            ->whereColumn('dr_prev.item_name', 'duel_releases.item_name')
            ->where(function ($q) {
                $q->whereColumn('dr_prev.date_release', '<', 'duel_releases.date_release')
                  ->orWhere(function ($q2) {
                      $q2->whereColumn('dr_prev.date_release', '=', 'duel_releases.date_release')
                         ->whereColumn('dr_prev.receipt_number', '<', 'duel_releases.receipt_number');
                  })
                  ->orWhere(function ($q3) {
                      $q3->whereColumn('dr_prev.date_release', '=', 'duel_releases.date_release')
                         ->whereColumn('dr_prev.receipt_number', '=', 'duel_releases.receipt_number')
                         ->whereColumn('dr_prev.id', '<', 'duel_releases.id');
                  });
            });

        $query->select([
            'duel_releases.id',
            'duel_releases.ministry_id',
            'duel_types.name_km as item_name',
            'duel_releases.receipt_number',
            'duel_releases.stock_number',
            'agencies.name as agency',
            'duel_releases.user_request',
            'duel_releases.receiver',
            'duel_releases.quantity_request',
            'duel_releases.note',
            'duel_releases.refer',
            'duel_releases.title',
            'duel_releases.date_release',
            'duel_releases.file',
            'duel_releases.created_at',
            'duel_releases.updated_at',
            DB::raw("GREATEST(0, COALESCE(duel_entries.quantity, 0) - ({$previousReleasesSubQuery->toSql()})) as running_total")
        ])
        ->mergeBindings($previousReleasesSubQuery);

        // Make sure table sort order matches subquery sequence logic
        return $query->orderBy('duel_releases.date_release', 'ASC')
            ->orderBy('duel_releases.receipt_number', 'ASC')
            ->orderBy('duel_releases.id', 'ASC');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::make('stock_number')->title(__('tables.th.stock.number'))->width(200)->addClass('align-middle'),
            Column::make('date_release')->title(__('tables.th.date.release'))->width(200)->addClass('align-middle'),
            Column::make('receipt_number')->title(__('tables.th.receipt.number'))->width(30)->addClass('align-middle'),
            Column::make('agency')->title(__('tables.th.agency'))->width(30)->addClass('align-middle'),
            Column::make('user_request')->title(__('tables.th.user.req'))->width(30)->addClass('align-middle'),
            Column::make('receiver')->title(__('tables.th.user.rec'))->width(30)->addClass('align-middle'),
            Column::make('item_name')->title(__('tables.th.item.name'))->width(90)->addClass('align-middle'),
            Column::make('quantity_total')->title(__('tables.th.quantity.total'))->addClass('align-middle'),
            Column::make('quantity_request')->title(__('tables.th.quantity.req'))->width(200)->addClass('align-middle'),
            Column::make('duel_total')->title(__('tables.th.quantity.remain'))->width(80)->addClass('align-middle'),
            Column::computed('refer')->title(__('tables.th.refer'))->width(200)->addClass('align-middle'),
            Column::computed('note')->title(__('tables.th.note'))->width(200)->addClass('align-middle'),
            Column::computed('title')->title(__('tables.th.title'))->width(200)->addClass('align-middle'),
            Column::computed('file')->title(__('tables.th.file'))->width(200)->addClass('align-middle'),
            Column::computed('action', __('tables.th.action'))->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('duelrelease-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                    d.cboNumber = $("#cboNumber").val();
                    d.start_date = $("#start_date").val();
                    d.end_date = $("#end_date").val();
                    d.cboUserRequest = $("#cboUserRequest").val();
                    d.cboDuelType = $("#cboDuelType").val();
                    d.cboExecutiveUnit = $("#cboExecutiveUnit").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#duelrelease-table").DataTable().ajax.reload();
                });
            }')
            ->parameters([
                'order' => [[2, 'asc'], [3, 'asc']], // Ensure DataTables orders by date_release & receipt_number on frontend
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ]);
    }

    protected function filename(): string
    {
        return 'DuelRelease_' . date('YmdHis');
    }
}

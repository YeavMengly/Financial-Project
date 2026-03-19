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
                return number_format($row->quantity_total ?? 0);
            })
            ->editColumn('quantity_request', function ($row) {
                return number_format($row->quantity_request ?? 0);
            })
            ->editColumn('duel_total', function ($row) {
                return number_format($row->duel_total ?? 0);
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->editColumn('item_name', function ($row) {
                return $row->item_name ?? '-';
            })
            ->addColumn('action', function ($module) {
                return view('duel::duelRelease.action', ['module' => $module]);
            })
            ->editColumn('file', function ($row) {
                if (!$row->attachments) {
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
                } else {
                    $url = asset('storage/uploads/' . $row->file);
                    return "<a href='$url' target='_blank' class='text-primary'><i class='fas fa-file-alt me-1'></i>Preview</a>";
                }
            })
            ->rawColumns(['note', 'refer', 'file']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DuelRelease $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $model = $model->newQuery();

        $model->leftJoin('agencies', 'duel_releases.agency', '=', 'agencies.id');
        $model->leftJoin('duel_types', 'duel_releases.item_name', '=', 'duel_types.id');

        $start = $request->start_date;
        $end = $request->end_date;

        // Apply date filter if both dates exist
        if ($start && $end) {
            $model->whereBetween('duel_releases.date_release', [$start, $end]);
        }

        // Optional: if only start date is set
        elseif ($start) {
            $model->whereDate('duel_releases.date_release', '>=', $start);
        }

        // Optional: if only end date is set
        elseif ($end) {
            $model->whereDate('duel_releases.date_release', '<=', $end);
        }

        $model->get();

        $model->select([
            'duel_releases.id',
            'duel_releases.ministry_id',
            'duel_types.name_km',
            'duel_releases.receipt_number',
            'duel_releases.stock_number',
            'agencies.name as agency',
            'duel_releases.user_request',
            'duel_releases.quantity_total',
            'duel_releases.quantity_request',
            'duel_releases.duel_total',
            'duel_releases.note',
            'duel_releases.refer',
            'duel_releases.title',
            'duel_releases.date_release',
            'duel_releases.file',
            'duel_releases.created_at',
            'duel_releases.updated_at',
        ])
            ->where('duel_releases.ministry_id', $id);

        $model->orderByDesc('duel_releases.created_at');

        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('duelrelease-table')
            ->columns($this->getColumns())
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->orderBy(2, 'ASC');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))
                ->width(30)->addClass('text-center align-middle')->orderable(false),

            Column::make('stock_number')->title(__('tables.th.stock.number'))->width(200)->addClass('align-middle'),
            Column::make('date_release')->title(__('tables.th.date.release'))->width(200)->addClass('align-middle'),
            Column::make('receipt_number')->title(__('tables.th.receipt.number'))->width(30)->addClass('align-middle'),
            Column::make('agency')->title(__('tables.th.agency'))->width(30)->addClass('align-middle'),
            Column::make('user_request')->title(__('tables.th.user.req'))->width(30)->addClass('align-middle'),
            Column::make('name_km')->title(__('tables.th.item.name'))->width(90)->addClass('align-middle'),
            Column::make('quantity_total')->title(__('tables.th.quantity.total'))->addClass('align-middle'),
            Column::make('quantity_request')->title(__('tables.th.quantity.req'))->width(200)->addClass('align-middle'),
            Column::make('duel_total')->title(__('tables.th.quantity.remain'))->width(80)->addClass('align-middle'),
            Column::computed('refer')->title(__('tables.th.refer'))->width(200)->addClass('align-middle'),
            Column::computed('note')->title(__('tables.th.note'))->width(200)->addClass('align-middle'),
            Column::computed('title')->title(__('tables.th.title'))->width(200)->addClass('align-middle'),
            Column::computed('file')->title(__('tables.th.file'))->width(200)->addClass('align-middle'),
            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'DuelRelease_' . date('YmdHis');
    }
}

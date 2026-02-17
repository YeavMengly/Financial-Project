<?php

namespace App\DataTables\Budget;

use App\Models\BudgetPlan\BudgetVoucher;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BudgetVoucherDataTable extends DataTable
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
            ->editColumn('agency', function ($row) {
                return $row->agency_no . ' - ' . $row->agency_name;
            })
            ->editColumn('budget', function ($row) {
                return number_format($row->budget ?? 0);
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->editColumn('t_name', function ($row) {
                return $row->t_name ?? '-';
            })
            ->addColumn('action', function ($module) {
                return view('budgetplan::budgetVoucher.action', ['module' => $module]);
            })
            ->editColumn('txtDescription', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->txtDescription) . '</div>';
            })
            ->editColumn('attachments', function ($row) {
                if (!$row->attachments) {
                    return '<span class="text-muted">-</span>';
                }
                $files = json_decode($row->attachments, true);
                if (is_array($files)) {
                    $html = '<ul class="list-unstyled m-0">';
                    foreach ($files as $file) {
                        $url = asset('storage/uploads/' . $file);
                        $html .= "<li><a href='$url' target='_blank' class='text-primary'><i class='fas fa-file-alt me-1'></i>$file</a></li>";
                    }
                    $html .= '</ul>';
                    return $html;
                } else {
                    $url = asset('storage/uploads/' . $row->attachments);
                    return "<a href='$url' target='_blank' class='text-primary'><i class='fas fa-file-alt me-1'></i>Preview</a>";
                }
            })
            ->rawColumns(['soft_delete', 'txtDescription','attachments'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BudgetVoucher $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $model = $model->newQuery();

        $model->leftJoin('account_subs', function ($join) use ($id) {
            $join->on('budget_vouchers.account_sub_id', '=', 'account_subs.no')
                ->where('account_subs.ministry_id', '=', $id);
        })->from('budget_vouchers');
        $model->leftJoin('agencies', 'budget_vouchers.agency_id', '=', 'agencies.id');
        $model->leftJoin('task_types', 'budget_vouchers.task_type', '=', 'task_types.id');

        // ===== FIXED CONDITION =====
        $model->where('budget_vouchers.ministry_id', $id);

        // ===== SELECT =====
        $model->select([
            'budget_vouchers.id',
            'budget_vouchers.ministry_id',
            'agencies.no AS agency_no',
            'agencies.name AS agency_name',
            'account_subs.no as account_sub_no',
            'budget_vouchers.no',
            'budget_vouchers.txtDescription',
            'budget_vouchers.budget',
            'budget_vouchers.legalNumber',
            'task_types.name AS t_name',
            'budget_vouchers.attachments',
            'budget_vouchers.date',
            'budget_vouchers.created_at'
        ]);

        $model->orderByDesc('budget_vouchers.created_at');

        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->ajax([
                'data' => 'function(d) {
                d.agency     = $("#agency").val();
                d.no    = $("#no").val();
                d.accountSub = $("#accountSub").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#budgetvoucher-table").DataTable().ajax.reload();
                });
            }')
            ->setTableId('budgetvoucher-table')
            ->columns($this->getColumns());
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))
                ->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::make('legalNumber')->title(__('tables.th.legal.number'))->width(90)->addClass('align-middle'),
            Column::make('agency')->title(__('tables.th.agency'))->width(90)->addClass('align-middle'),
            Column::make('account_sub_no')->title(__('tables.th.sub.account'))->width(30)->addClass('align-middle'),
            Column::make('no')->title(__('tables.th.program'))->width(60)->addClass('align-middle'),
            Column::make('t_name')->title(__('tables.th.type'))->width(60)->addClass('align-middle'),
            Column::make('budget')->title(__('tables.th.budget'))->width(80)->addClass('align-middle'),
            Column::make('date')->title(__('tables.th.date'))->width(80)->addClass('align-middle'),
            Column::make('txtDescription')->title(__('tables.th.description'))->addClass('align-middle'),
            Column::make('attachments')->title(__('tables.th.document.title'))->width(200)->addClass('align-middle'),
            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }


    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BudgetVoucher_' . date('YmdHis');
    }
}

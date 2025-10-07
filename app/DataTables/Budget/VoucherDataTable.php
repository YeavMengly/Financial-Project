<?php

namespace App\DataTables\Budget;

use App\Models\BeginCredit\InitialBudget;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\BudgetPlan\InitialVoucher;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class VoucherDataTable extends DataTable
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
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->editColumn('task_type', function ($row) {
                return $row->task_name ?? '-';
            })
            ->addColumn('action', function ($module) {
                return view('budgetplan::voucher.action', ['module' => $module]);
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
            ->rawColumns(['txtDescription', 'attachments']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(BudgetVoucher $model,  Request $request): QueryBuilder
    {
        // $params = $request->params;
        // $id = decode_params($params);

        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('budgetvoucher-table')
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

            Column::make('')->title(__('tables.th.agency'))->width(30)->addClass('align-middle'),
            Column::make('')->title(__('tables.th.sub.account'))->width(30)->addClass('align-middle'),
            Column::make('')->title(__('tables.th.program'))->width(30)->addClass('align-middle'),
            Column::make('')->title(__('tables.th.budget'))->width(80)->addClass('align-middle'),
            Column::make('')->title(__('tables.th.type'))->width(60)->addClass('align-middle'),
            Column::make('')->title(__('tables.th.date'))->width(80)->addClass('align-middle'),
            Column::make('')->title(__('tables.th.description'))->addClass('align-middle'),
            Column::make('')->title(__('tables.th.document.title'))->width(200)->addClass('align-middle'),

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

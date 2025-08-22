<?php

namespace App\DataTables\Budget;

use App\Models\BeginCredit\InitialBudget;
use App\Models\BudgetPlan\BudgetVoucher;
use App\Models\BudgetPlan\InitialVoucher;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
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
    public function query(BudgetVoucher $model): QueryBuilder
    {
        $year = null;

        if ($id = request('year')) {
            $initial = InitialBudget::find($id);
            if ($initial) {
                $year = $initial->id;
            }
        }

        $query = $model->newQuery()
            ->leftJoin('task_types', 'budget_vouchers.task_type', '=', 'task_types.task')
            ->select([
                'budget_vouchers.id',
                'budget_vouchers.subAccountNumber as CNA',
                'budget_vouchers.program as SNA',
                'budget_vouchers.budget',
                'budget_vouchers.task_type',
                'task_types.task AS task_name',
                'budget_vouchers.txtDescription',
                'budget_vouchers.attachments',
                'budget_vouchers.date',
                'budget_vouchers.year',
            ]);

        // Year filter
        if ($year) {
            $query->where('budget_vouchers.year', $year);
        }

        // subAccountNumber
        if (request()->filled('subAccountNumber')) {
            $query->where('budget_vouchers.subAccountNumber', request('subAccountNumber'));
        }

        // program
        if (request()->filled('program')) {
            $query->where('budget_vouchers.program', request('program'));
        }

        // task_type (compared by string task name)
        if (request()->filled('task_type')) {
            $query->where('budget_vouchers.task_type', request('task_type'));
        }

        // description
        if (request()->filled('description')) {
            $query->where('budget_vouchers.txtDescription', 'like', '%' . request('description') . '%');
        }

        // date range
        if (request()->filled('start_date') && request()->filled('end_date')) {
            $query->whereBetween('budget_vouchers.date', [request('start_date'), request('end_date')]);
        } elseif (request()->filled('start_date')) {
            $query->whereDate('budget_vouchers.date', '>=', request('start_date'));
        } elseif (request()->filled('end_date')) {
            $query->whereDate('budget_vouchers.date', '<=', request('end_date'));
        }

        return $query->orderByDesc('budget_vouchers.created_at');
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

            Column::make('CNA')->title(__('tables.th.sub.account'))->width(30)->addClass('align-middle'),
            Column::make('SNA')->title(__('tables.th.program'))->width(30)->addClass('align-middle'),
            Column::make('budget')->title(__('tables.th.budget'))->width(80)->addClass('align-middle'),
            Column::make('task_name')->title(__('tables.th.type'))->width(60)->addClass('align-middle'),
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

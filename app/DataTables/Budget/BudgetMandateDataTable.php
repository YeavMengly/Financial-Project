<?php

namespace App\DataTables\Budget;

use App\Models\BeginCredit\InitialBudget;
use App\Models\BudgetPlan\BudgetMandate;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BudgetMandateDataTable extends DataTable
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
                return view('budgetplan::mandate.action', ['module' => $module]);
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
    public function query(BudgetMandate $model): QueryBuilder
    {
        $initialVoucherId = request()->get('year');
        $year = null;

        if ($initialVoucherId) {
            $initialBudget = InitialBudget::find($initialVoucherId);
            if ($initialBudget) {
                $year = $initialBudget->id;
            }
        }

        $query = $model->newQuery()
            ->leftJoin('task_types', 'budget_mandates.task_type', '=', 'task_types.task')
            ->select([
                'budget_mandates.id',
                'budget_mandates.subAccountNumber as CNA',
                'budget_mandates.program as SNA',
                'budget_mandates.budget',
                'budget_mandates.task_type',
                'task_types.task AS task_name',
                'budget_mandates.txtDescription',
                'budget_mandates.attachments',
                'budget_mandates.date',
                'budget_mandates.year'
            ]);

        if ($year) {
            $query->where('budget_mandates.year', $year);
        }

        if (request()->filled('subAccountNumber')) {
            $query->where('budget_mandates.subAccountNumber', request('subAccountNumber'));
        }

        if (request()->filled('program')) {
            $query->where('budget_mandates.program', request('program'));
        }

        if (request()->filled('task_type')) {
            $query->where('budget_mandates.task_type', request('task_type'));
        }

        if (request()->filled('description')) {
            $query->where('budget_mandates.txtDescription', 'like', '%' . request('description') . '%');
        }

        if (request()->filled('start_date') && request()->filled('end_date')) {
            $query->whereBetween('budget_mandates.date', [request('start_date'), request('end_date')]);
        } elseif (request()->filled('start_date')) {
            $query->whereDate('budget_mandates.date', '>=', request('start_date'));
        } elseif (request()->filled('end_date')) {
            $query->whereDate('budget_mandates.date', '<=', request('end_date'));
        }

        return $query->orderBy('budget_mandates.created_at', 'DESC');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('budgetmandate-table')
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
        // return [
        //     Column::computed('DT_RowIndex', __('tables.th.no'))
        //         ->width(30)->addClass('text-center align-middle')->orderable(false),

        //     Column::make('CNA')->title(__('tables.th.sub.account'))->addClass('align-middle'),
        //     Column::make('SNA')->title(__('tables.th.program'))->addClass('align-middle'),
        //     Column::make('budget')->title(__('tables.th.budget'))->addClass('align-middle'),
        //     Column::make('task_name')->title(__('tables.th.type'))->addClass('align-middle'),
        //     Column::make('date')->title(__('tables.th.date'))->addClass('align-middle'),
        //     Column::make('txtDescription')->title(__('tables.th.description'))->addClass('align-middle'),
        //     Column::make('attachments')->title(__('tables.th.document.title'))->addClass('align-middle'),

        //     Column::computed('action', __('tables.th.action'))
        //         ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        // ];
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
        return 'BudgetMandate_' . date('YmdHis');
    }
}

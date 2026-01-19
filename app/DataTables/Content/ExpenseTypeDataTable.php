<?php

namespace App\DataTables\Content;

use App\Models\Content\ExpenseType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ExpenseTypeDataTable extends DataTable
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
            ->editColumn('status', function ($row) {
                if ($row->status) {
                    return '<span class="badge bg-success">Active</span>';
                }
                return '<span class="badge bg-danger">Inactive</span>';
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __("buttons.active") . '</span>' : '<span class="badge bg-danger">' . __("buttons.deleted") . '</span>';
                return $active;
            })
            ->addColumn("dateTime", function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d  h:i:s A');
            })
            ->addColumn('action', function ($row) {
                return view('content::content.expenseType.action', [
                    'module' => $row,
                ]);
            })
            ->rawColumns(['status', 'soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ExpenseType $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->withTrashed();
        $model->newQuery()
            ->select([
                'expense_types.id',
                'expense_types.name_kh',
                'expense_types.name_en',
                'expense_types.status',
                'expense_types.created_at',
                'expense_types.deleted_at'
            ])
            ->orderBy('created_at', 'ASC');
        return $model->orderBy('expense_types.id', 'ASC');

        /**
         * ================       Step 2:  Filter by chapter number if provided        ================
         */
        $model->where('chapters.ministry_id', $id);

        return $model->orderBy('chapters.id', 'ASC');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('expensetype-table')
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->columns($this->getColumns())
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

            Column::make('name_kh')->title(__('tables.th.name.kh'))->addClass('align-middle'),
            Column::make('name_en')->title(__('tables.th.name.en'))->addClass('align-middle'),
            Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),
            Column::computed('status')
                ->title(__('tables.th.status'))
                ->width(100)
                ->addClass('text-center align-middle'),

            Column::computed('soft_delete')->title(__('tables.th.status'))->width(100)->addClass('text-center'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),

        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ExpenseType_' . date('YmdHis');
    }
}

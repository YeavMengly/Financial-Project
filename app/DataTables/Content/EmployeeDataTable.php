<?php

namespace App\DataTables\Content;

use App\Models\Content\Employee;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class EmployeeDataTable extends DataTable
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
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __("buttons.active") . '</span>' : '<span class="badge bg-danger">' . __("buttons.deleted") . '</span>';
                return $active;
            })
            ->addColumn("dateTime", function ($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })
            ->addColumn('action', function ($module) {

                return view('content::content.employees.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Employee $model, Request $request): QueryBuilder
    {
        $query = $model->newQuery();
        $query->withTrashed();
        $query->select([
            'employees.id',
            'employees.id_number',
            'employees.account_number',
            'employees.name_kh',
            'employees.name_latin',
            'employees.created_at',
            'employees.deleted_at',
        ]);

        if ($request->filled('employeeName')) {
            $searchTerm = '%' . trim($request->input('employeeName')) . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('employees.name_kh', 'LIKE', $searchTerm)
                    ->orWhere('employees.name_latin', 'LIKE', $searchTerm);
            });
        }

        return $query->orderBy('employees.id', 'ASC');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('employee-table')
            ->columns($this->getColumns())
            ->ajax([
                'url'  => route('employees.index'), // Replace with your route if different
                'type' => 'GET',
                'data' => 'function(d) {
                d.employeeName = $("#employeeName").val();
            }',
            ])
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->orderBy(0, 'ASC');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('ល.រ')
                ->addClass('text-center')
                ->width(60)
                ->searchable(false)
                ->orderable(false),
            Column::make('id_number')->title(__('tables.th.id.number'))->width(60)->addClass('align-middle'),
            Column::make('account_number')->title(__('tables.th.id.account'))->addClass('align-middle'),
            Column::make('name_kh')->title(__('tables.th.name.kh'))->width(200),
            Column::make('name_latin')->title(__('tables.th.name.en'))->width(100)->addClass('text-center'),
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
        return 'Employee_' . date('YmdHis');
    }
}

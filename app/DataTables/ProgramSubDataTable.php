<?php

namespace App\DataTables;

use Illuminate\Http\Request;
use App\Models\ProgramSub;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProgramSubDataTable extends DataTable
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
                $active = (is_null($soft_delete->delete_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->editColumn('decription', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->decription) . '</div>';
            })
            ->rawColumns(['decription'])
            ->addColumn('action', function ($module) {
                return view('beginningcredit::programs.programSub.action', ['module' => $module]);
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */

    public function query(ProgramSub $model, Request $request): QueryBuilder
    {
        // return $model->newQuery();
        $params = $request->params;
        $id = decode_params($params);

        $query = $model->newQuery()
            ->select([
                'program_subs.ministry_id',
                'program_subs.program_id',
                'program_subs.no',
                'program_subs.decription',
            ])
            ->orderBy('program_subs.no', 'ASC');

        $query->where('program_subs.ministry_id', $id);

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('programsub-table')
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
            Column::make('program_id')->title(__('tables.th.depart'))->width(60)->addClass('align-middle'),

            Column::make('no')->title(__('tables.th.sub.depart'))->width(60)->addClass('align-middle'),
            Column::make('decription')->title(__('tables.th.title'))->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ProgramSub' . date('YmdHis');
    }
}

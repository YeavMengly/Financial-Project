<?php

namespace App\DataTables;

use App\Models\SubDepart;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class SubDepartDataTable extends DataTable
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
            ->editColumn('txtSubDepart', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->txtSubDepart) . '</div>';
            })
            ->rawColumns(['txtSubDepart'])
            ->addColumn('action', function ($module) {
                return view('beginningcredit::depart.subDepart.action', ['module' => $module]);
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(SubDepart $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->select([
                'id',
                'depart_id',
                'subDepart',
                'txtSubDepart',
                'created_at',
                'updated_at',
            ])
            ->orderBy('created_at', 'ASC');

        // Filter by subDepart
        if (request()->filled('subDepart')) {
            $query->where('subDepart', request('subDepart'));
        }

        // Filter by txtSubDepart (partial match)
        if (request()->filled('txtSubDepart')) {
            $query->where('txtSubDepart', 'like', '%' . request('txtSubDepart') . '%');
        }

        return $query;
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('subdepart-table')
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
            Column::make('depart_id')->title(__('tables.th.depart'))->width(60)->addClass('align-middle'),

            Column::make('subDepart')->title(__('tables.th.sub.depart'))->width(60)->addClass('align-middle'),
            Column::make('txtSubDepart')->title(__('tables.th.title'))->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SubDepart_' . date('YmdHis');
    }
}

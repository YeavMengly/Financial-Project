<?php

namespace App\DataTables;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ChapterDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {

        return (new EloquentDataTable($query))
            ->addIndexColumn()->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->delete_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->addColumn('action', function ($module) {
                return view('beginningcredit::chapter.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Chapter $model, Request $request): QueryBuilder
    {
        /**
         * ================       Step 1:  Start a new query with selected columns        ================
         */
        $params = $request->params;
        $id = decode_params($params);

        $query = $model->newQuery()
            ->select([
                'id',
                'ministry_id',
                'no',
                'name',
            ])
            ->orderBy('created_at', 'ASC');

        /**
         * ================       Step 2:  Filter by chapter number if provided        ================
         */
        $query->where('chapters.ministry_id', $id);

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('chapter-table')
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

            Column::make('no')->title(__('tables.th.chapter'))->addClass('align-middle'),
            Column::make('name')->title(__('tables.th.txtChapter'))->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Chapter_' . date('YmdHis');
    }
}

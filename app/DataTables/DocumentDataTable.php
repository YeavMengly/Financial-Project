<?php

namespace App\DataTables;

use App\Models\Document;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DocumentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';

                //$active = $active.'<br />'.Carbon::parse($soft_delete->created_at)->format('Y-m-d  h:i:s A');
                return $active;
            })
            ->addColumn('action', function ($module) {
                return view('document::action', ['module' => $module]);
            })
            ->addColumn('description', function ($module) {
                return '<strong>' . $module->title . '</strong><br/><hr/>' . $module->description;
            })
            ->rawColumns(['soft_delete', 'description']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Document $model, Request $request): QueryBuilder
    {
        $model = $model->newQuery();
        $model->join('categories', 'documents.cate_id', '=', 'categories.id');
        $model->join('category_subs', 'documents.sub_id', '=', 'category_subs.id');

        if ($request->cboCategory) {
            $model->where('documents.cate_id', $request->cboCategory);
        }

        if ($request->cboCategorySub) {
            $model->where('documents.sub_id', $request->cboCategorySub);
        }
        if ($request->cboStatus) {
            if ($request->cboStatus == '1') {
                $model->where('documents.deleted_at', null);
            } else {
                $model->where('documents.deleted_at', '!=', null);
            }
        } else {
            $model->withTrashed();
        }

        $model->select(
            [
                'documents.id',
                'categories.name AS CNA',
                'category_subs.name AS SNA',
                'documents.year',
                'documents.title',
                'documents.description',
                'documents.fileName',
                'documents.deleted_at',
                'documents.created_at',
            ]
        );
        $model->orderBy('documents.created_at', 'DESC');

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
                    d.cboCategory = $("#cboCategory").val();
                    d.cboCategorySub = $("#cboCategorySub").val();
                    d.cboStatus = $("#cboStatus").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#document-table").DataTable().ajax.reload();
                });
            }')
            ->setTableId('document-table')
            ->columns($this->getColumns());
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed(
                'DT_RowIndex',
                __('tables.th.no')
            )->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::computed('CNA')->title(__('tables.th.category'))->addClass('align-middle'),
            Column::computed('SNA')->title(__('tables.th.category.sub'))->addClass('align-middle'),
            Column::make('year')->title(__('tables.th.year'))->width(80)->addClass('align-middle'),
            Column::make('description')->title(__('tables.th.document.title'))->addClass('align-middle'),

            Column::computed('soft_delete')->title(__('tables.th.status'))->width(100)->addClass('text-center align-middle'),
            Column::computed(
                'action',
                __('tables.th.action')
            )->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Document_' . date('YmdHis');
    }
}

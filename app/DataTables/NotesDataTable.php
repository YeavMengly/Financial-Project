<?php

namespace App\DataTables;

use App\Models\Notes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class NotesDataTable extends DataTable
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
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">'.__('buttons.active').'</span>' : '<span class="badge bg-danger">'.__('buttons.deleted').'</span>';
                $active = $active.'<br />'.Carbon::parse($soft_delete->created_at)->format('Y-m-d  h:i:s A');

                return $active;
            })
            ->editColumn('is_archived', function ($module) {
                $notes = ($module->is_archived == 2) ? '<button class="btn btn-sm btn-outline-success">បានបញ្ចប់</button>' : '<button class="btn btn-sm btn-outline-primary">កំពុងធ្វើ</button>';

                return $notes;
            })
            ->addColumn('action', function ($module) {
                return view('notes::action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'description', 'is_archived']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Notes $model, Request $request): QueryBuilder
    {
        $model = $model->newQuery();
        if ($request->cboStatus) {
            if ($request->cboStatus == '2') {
                $model->where('deleted_at', null);
            } elseif ($request->cboStatus == '3') {
                $model->where('deleted_at', '!=', null);
            } else {
                $model->withTrashed();
            }
        } else {
            $model->where('deleted_at', null);
        }

        if ($request->cboTodo) {
            if ($request->cboTodo == 2) {
                $model->where('is_archived', 1);
            } elseif ($request->cboTodo == 3) {
                $model->where('is_archived', 2);
            }
        } else {
            $model->where('is_archived', 1);
        }

        $model->select([
            'id',
            'title',
            'description',
            'created_at',
            'deleted_at',
            'is_archived',
        ]);
        $model->orderBy('created_at', 'DESC');

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
                    d.cboTodo = $("#cboTodo").val();
                    d.cboStatus = $("#cboStatus").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#notes-table").DataTable().ajax.reload();
                });
                var tr = document.createElement("tr");
                var columns = this.api().init().columns;
                this.api().columns().every(function (index) {
                    var column = this;
                    var td = document.createElement("td");
                    if (columns[index] && columns[index].searchable) {
                        var input = document.createElement("input");
                        input.className = "form-control form-control-sm";
                        $(input).on("change", function () {
                            column.search($(this).val(), false, false, true).draw();
                        }).appendTo(td);
                    }
                    $(td).appendTo(tr);
                });
                $(".table-responsive table thead").append(tr);
            }')
            ->setTableId('notes-table')
            ->columns($this->getColumns());
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex',
                __('tables.th.no'))->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::make('title')->title(__('tables.th.notes'))->addClass('align-middle'),
            Column::make('description')->title(__('tables.th.description'))->addClass('align-middle'),
            Column::computed('is_archived')->title(__('Task'))->width(100)->addClass('text-center align-middle'),
            Column::computed('soft_delete')->title(__('tables.th.status'))->width(100)->addClass('text-center align-middle'),
            Column::computed('action',
                __('tables.th.action'))->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Notes_'.date('YmdHis');
    }
}

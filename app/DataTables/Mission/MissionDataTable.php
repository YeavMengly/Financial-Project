<?php

namespace App\DataTables\Mission;

use App\Models\Mission\Mission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MissionDataTable extends DataTable
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
            ->addColumn("dateTime", function ($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })
            ->editColumn('txtDescription', function ($row) {
                return '<div style="max-height: 40px; overflow-x: auto; white-space: normal;">' . e($row->txtDescription) . '</div>';
            })
            ->rawColumns(['txtDescription', 'soft_delete', 'agency'])
            ->addColumn('action', function ($module) {
                return view('mission::missions.action', ['module' => $module]);
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Mission $model, Request $request): QueryBuilder
    {
        // ===== Fetch Parameters =====
        $params = request()->params;
        $id = decode_params($params);
        $model = $model->newQuery();

        // ===== Join Tables =====

        $model->leftJoin('ministries', 'missions.ministry_id', '=', 'ministries.id');
        $model->leftJoin('employees', 'missions.employee_id', '=', 'employees.id');
        $model->leftJoin('positions', 'missions.position_id', '=', 'positions.id');
        $model->leftJoin('levels', 'missions.level_id', '=', 'levels.id');
        $model->leftJoin('provinces', 'missions.province_id', '=', 'provinces.id');

        // ===== Filter Data =====
        if ($request->has('cboTodo') && $request->cboTodo != '') {
            $model->where('missions.is_archived', $request->cboTodo);
        }
        if ($request->has('cboStatus') && $request->cboStatus != '') {
            $model->where('missions.soft_delete', $request->cboStatus);
        }
        if ($request->has('cboName') && $request->cboName != '') {
            $model->where('missions.employee_id', $request->cboName);
        }
        if ($request->has('cboPosition') && $request->cboPosition != '') {
            $model->where('missions.position_id', $request->cboPosition);
        }
        if ($request->has('cboLevel') && $request->cboLevel != '') {
            $model->where('missions.level_id', $request->cboLevel);
        }
        if ($request->has('cboProvince') && $request->cboProvince != '') {
            $model->where('missions.province_id', $request->cboProvince);
        }
        if ($request->has('start_date') && $request->start_date != '') {
            $model->whereDate('missions.legal_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $model->whereDate('missions.legal_date', '<=', $request->end_date);
        }

        // ===== FIXED CONDITION =====
        $model->where('missions.ministry_id', $id);

        // ===== Select Columns =====   
        $model->select(
            'missions.*',
            'ministries.name as ministry_name',
            'employees.name as employee_name',
            'positions.name as position_name',
            'levels.name as level_name',
            'provinces.name as province_name'

        );

        // ===== Order Columns =====
        $model->orderBy('missions.created_at', 'asc');

        // ===== Default Order =====
        if (!$request->has('order')) {
            $model->orderBy('missions.legal_number', 'asc')
                ->orderBy('missions.no', 'asc');
        }

        // ===== Return Model =====
        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('mission-table')
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->ajax([
                'data' => 'function(d) {
                d.cboTodo     = $("#cboTodo").val();
                d.cboStatus   = $("#cboStatus").val();
                d.cboName     = $("#cboName").val();
                d.cboPosition = $("#cboPosition").val();
                d.cboLevel    = $("#cboLevel").val();
                d.cboProvince = $("#cboProvince").val();
                d.start_date  = $("#start_date").val();
                d.end_date    = $("#end_date").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#mission-table").DataTable().ajax.reload();
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

            Column::make('legal_number')->title(__('tables.th.legal.number'))->width(30)->addClass('align-middle'),
            Column::make('legal_date')->title(__('tables.th.date.legal'))->width(30)->addClass('align-middle'),
            Column::make('description')->title(__('tables.th.description'))->addClass('align-middle'),
            Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Mission_' . date('YmdHis');
    }
}

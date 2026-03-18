<?php

namespace App\DataTables\Electric;

use App\Models\Content\Ministry;
use Carbon\Carbon;
use App\Models\InitialElectricEntity;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InitialElectricEntityDataTable extends DataTable
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
            ->editColumn('soft_delete', function ($module) {
                return is_null($module->deleted_at)
                    ? '<span class="badge bg-success">' . __('buttons.active') . '</span>'
                    : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
            })
            ->editColumn('is_archived', function ($module) {
                $notes = ($module->is_archived == 2) ? '<button class="btn btn-sm btn-outline-success">បានបញ្ចប់</button>' : '<button class="btn btn-sm btn-outline-primary">កំពុងធ្វើ</button>';

                return $notes;
            })
            ->addColumn("dateTime", function ($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })
            ->addColumn('action', function ($model) {
                return view('electric::electric.entity.initialElectricEntity.action', ['module' => $model]);
            })
            ->rawColumns(['status', 'soft_delete', 'action', 'is_archived']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Ministry $model, Request $request): QueryBuilder
    {
        $model = $model->newQuery();

        if ($request->cboStatus) {
            if ($request->cboStatus == '2') {
                $model->where('ministries.deleted_at', null);
            } elseif ($request->cboStatus == '3') {
                $model->where('ministries.deleted_at', '!=', null);
            } else {
                $model->withTrashed();
            }
        } else {
            $model->where('ministries.deleted_at', null);
        }

        if ($request->cboTodo) {
            if ($request->cboTodo == 2) {
                $model->where('ministries.is_archived', 1);
            } elseif ($request->cboTodo == 3) {
                $model->where('ministries.is_archived', 2);
            }
        } else {
            $model->where('ministries.is_archived', 1);
        }

        $model->select([
            'ministries.id',
            'ministries.no',
            'ministries.year',
            'ministries.title',
            'ministries.refer',
            'ministries.name',
            'ministries.status',
            'ministries.is_archived',
            'ministries.created_at',
            'ministries.deleted_at'
        ]);

        return $model->orderBy('ministries.id', 'DESC');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('initialelectricentity-table')
            ->columns($this->getColumns())
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],

            ])->ajax([
                'data' => 'function(d) {
                    d.cboTodo = $("#cboTodo").val();
                    d.cboStatus = $("#cboStatus").val();
                }',
            ])
            ->initComplete('function () {
                $("#filter").submit(function(event) {
                    event.preventDefault();
                    $("#initialelectricentity-table").DataTable().ajax.reload();
                });
            }')
            ->orderBy(2, 'ASC');
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
            Column::computed('is_archived')->title(__('Task'))->width(100)->addClass('text-center align-middle'),

            Column::make('year')->title(__('tables.th.year'))->width(80)->addClass('align-middle'),
            Column::make('title')->title(__('tables.th.title'))->addClass('align-middle'),
            Column::make('refer')->title(__('tables.th.refer'))->addClass('align-middle'),
            Column::make('name')->title(__('tables.th.description'))->addClass('align-middle'),

            Column::computed('soft_delete')->title(__('tables.th.status'))->width(100)->addClass('text-center'),
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
        return 'InitialElectricEntity_' . date('YmdHis');
    }
}

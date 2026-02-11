<?php

namespace App\DataTables\Electric;

use App\Models\Electric\ElectricEntity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ElectricEntityDataTable extends DataTable
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
                return view('electric::electric.entity.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ElectricEntity $model,  Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);
        $model = $model->newQuery();
        $model->withTrashed();
        $model->newQuery()
            ->leftJoin('provinces', 'electric_entities.province_id', '=', 'provinces.id',)
            ->select([
                'electric_entities.id',
                'electric_entities.ministry_id',
                'electric_entities.title_entity',
                'electric_entities.location_number',
                'provinces.name',
                'electric_entities.created_at',
                'electric_entities.updated_at',
                'electric_entities.deleted_at',
            ])->where('electric_entities.ministry_id', $id)
            ->orderBy('electric_entities.created_at', 'ASC');
        $model->where('electric_entities.ministry_id', $id);

        return $model->orderBy('electric_entities.id', 'ASC');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('electricentity-table')
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
        return [
            Column::computed('DT_RowIndex', __('tables.th.no'))
                ->width(30)->addClass('text-center align-middle')->orderable(false),

            Column::make('title_entity')->title(__('tables.th.title_entity'))->addClass('align-middle'),
            Column::make('location_number')->title(__('tables.th.location_number'))->addClass('align-middle'),
            Column::make('name')->title(__('tables.th.province'))->addClass('align-middle'),
            Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),
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
        return 'ElectricEntity_' . date('YmdHis');
    }
}

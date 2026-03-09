<?php

namespace App\DataTables\Water;

use App\Models\Water\Water;
use App\Models\Water\Water as ModelsWater;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class WaterDataTable extends DataTable
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
                return view('water::water.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Water $model,  Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);
        // $model = $model->newQuery();
        // $model->withTrashed();
        $query = $model->newQuery()
            ->select([
                'waters.id',
                'waters.ministry_id',
                'waters.title_entity',
                'waters.location_number_use',
                'waters.invoice',
                'waters.date',
                'waters.use_start',
                'waters.use_end',
                'waters.kilo',
                'waters.cost_total',
                'waters.created_at',
                'waters.updated_at',
                'waters.deleted_at',
            ])->where('waters.ministry_id', $id);

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('water-table')
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
            Column::make('location_number_use')->title(__('tables.th.location_number'))->addClass('align-middle'),
            Column::make('invoice')->title(__('tables.th.invoice'))->addClass('align-middle'),
            Column::make('date')->title(__('tables.th.date'))->addClass('align-middle'),
            Column::make('use_start')->title(__('tables.th.use.start'))->addClass('align-middle'),
            Column::make('use_end')->title(__('tables.th.use.end'))->addClass('align-middle'),
            Column::make('kilo')->title(__('tables.th.kilo'))->addClass('align-middle'),
            Column::make('cost_total')->title(__('tables.th.cost.total'))->addClass('align-middle'),
            // Column::make('dateTime')->title(__('tables.th.createdAt'))->width(200),
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
        return 'Water_' . date('YmdHis');
    }
}

<?php

namespace App\DataTables\Content;


use Illuminate\Http\Request;
use App\Models\Content\ExecutiveUnit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ExecutiveUnitDataTable extends DataTable
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
                return view('content::content.agency.executiveUnit.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ExecutiveUnit $model,  Request $request): QueryBuilder
    {
        $params = $request->params;
        $executiveId = $request->executiveId;

        $id = decode_params($params);
        $executiveId = decode_params($executiveId);

        $model = $model->newQuery();
        $model->withTrashed();
        $model->leftJoin('ministries', 'executive_units.ministry_id', '=', 'ministries.id');

        $query = $model->newQuery()
            ->leftJoin('agencies', 'executive_units.agency_id', '=', 'agencies.id')
            ->select([
                'executive_units.id',
                'executive_units.ministry_id',
                'agencies.id as agency_id',
                'executive_units.title',
                'executive_units.created_at',
                'executive_units.updated_at',
                'executive_units.deleted_at',
                'ministries.is_archived'
            ])
            ->where('executive_units.ministry_id', $id)
            ->where('executive_units.agency_id', $executiveId)
            ->orderBy('executive_units.id', 'ASC');


        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('executiveunit-table')
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

            Column::make('title')->title(__('tables.th.agency.executive.unit'))->width(60)->addClass('align-middle'),
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
        return 'ExecutiveUnit_' . date('YmdHis');
    }
}

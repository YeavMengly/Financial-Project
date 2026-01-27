<?php

namespace App\DataTables\Content;


use Illuminate\Http\Request;
use App\Models\Content\Cluster;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ClusterDataTable extends DataTable
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
            ->editColumn('decription', function ($row) {
                return '<div style="max-height: 60px; overflow-x: auto; white-space: normal;">' . e($row->decription) . '</div>';
            })
            ->rawColumns(['decription'])
            ->addColumn('action', function ($module) {

                return view('content::content.program.sub.cluster.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action', 'decription']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Cluster $model,  Request $request): QueryBuilder
    {
        // return $model->newQuery();
        $params = $request->params;
        $pId = $request->pId;
        $pSubId = $request->pSubId;

        $id = decode_params($params);
        $pId = decode_params($pId);
        $pSubId = decode_params($pSubId);

        $model = $model->newQuery();
        $model->withTrashed();
        $query = $model->newQuery()
            // ->leftJoin('programs', 'clusters.program_id', '=', 'programs.id')
            // ->leftJoin('program_subs', 'clusters.program_sub_id', '=', 'program_subs.id')
            ->select([
                'clusters.id',
                'clusters.ministry_id',
                'clusters.program_id',
                'clusters.program_sub_id',
                'clusters.no',
                'clusters.decription',
                'clusters.created_at',
                'clusters.deleted_at',
            ])
            ->orderBy('clusters.no', 'ASC');

        $query->where('clusters.ministry_id', $id);
        // $query->where('clusters.program_id', $pId);
        // $query->where('clusters.program_sub_id', $pSubId);

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('cluster-table')
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

            Column::make('no')->title(__('tables.th.cluster'))->width(60)->addClass('align-middle'),
            Column::make('decription')->title(__('tables.th.title'))->addClass('align-middle'),
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
        return 'Cluster_' . date('YmdHis');
    }
}

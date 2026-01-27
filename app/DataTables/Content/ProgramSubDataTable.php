<?php

namespace App\DataTables\Content;

use Illuminate\Http\Request;
use App\Models\Content\ProgramSub;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProgramSubDataTable extends DataTable
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
                return view('content::content.program.sub.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */

    public function query(ProgramSub $model, Request $request): QueryBuilder
    {
        // return $model->newQuery();
        $params = $request->params;
        $pId = $request->pId;
        $id = decode_params($params);
        $pId = decode_params($pId);

        $model = $model->newQuery();
        $model->withTrashed();
        $query = $model->newQuery()
            ->leftJoin('programs', 'program_subs.program_id', '=', 'programs.id')
            ->select([
                'program_subs.id',
                'program_subs.ministry_id',
                'programs.no as program_id',
                'program_subs.no',
                'program_subs.decription',
                'program_subs.created_at',
                'program_subs.deleted_at',
            ])
            ->orderBy('program_subs.no', 'ASC');

        $query->where('program_subs.ministry_id', $id);
        $query->where('program_subs.program_id', $pId);

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('programsub-table')
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

            Column::make('no')->title(__('tables.th.sub.program'))->width(60)->addClass('align-middle'),
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
        return 'ProgramSub' . date('YmdHis');
    }
}

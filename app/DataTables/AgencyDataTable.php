<?php

namespace App\DataTables;

use App\Models\BeginCredit\Agency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AgencyDataTable extends DataTable
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
                return view('beginningcredit::agency.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Agency $model,  Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);
        $model = $model->newQuery();
        $model->withTrashed();
        $model->newQuery()
            ->leftJoin('programs', 'agencies.program_id', '=', 'programs.id')
            ->select([
                'agencies.id',
                'agencies.ministry_id',
                'agencies.program_id',
                'agencies.no as name_no',
                'agencies.name',
                'agencies.nick_name',
                'programs.no as no_program',
                'agencies.created_at',
                'agencies.deleted_at'

            ])->where('agencies.ministry_id', $id)
            ->orderBy('agencies.created_at', 'ASC');
        $model->where('agencies.ministry_id', $id);

        return $model->orderBy('agencies.id', 'ASC');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('agency-table')
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
                
            Column::make('no_program')->title(__('tables.th.program'))->addClass('align-middle'),
            Column::make('name_no')->title(__('tables.th.agency'))->addClass('align-middle'),
            Column::make('name')->title(__('tables.th.title'))->addClass('align-middle'),
            Column::make('nick_name')->title(__('tables.th.nick_name'))->addClass('align-middle'),
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
        return 'Agency_' . date('YmdHis');
    }
}

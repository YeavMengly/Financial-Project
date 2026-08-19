<?php

namespace App\DataTables\Duel;

use App\Models\Duel\DuelEntry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DuelEntryDataTable extends DataTable
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
            ->editColumn('quantity', function ($row) {
                return number_format($row->quantity ?? 0) . ' L';
            })
            ->editColumn('price', function ($row) {
                return number_format($row->price ?? 0) . ' ៛';
            })
            ->editColumn('date_entry', function ($row) {
                $active =  Carbon::parse($row->date_entry)->format('Y-m-d');

                return $active;
            })
            ->editColumn('file', function ($row) {
                return '<strong>' . $row->title  . '</strong><br/><hr/>' . $row->file;
            })
            ->editColumn('duel_total', function ($row) {
                return number_format($row->duel_total ?? 0) . ' ៛';
            })
            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })
            ->addColumn('action', function ($module) {
                return view('duel::duelEntry.action', ['module' => $module]);
            })
            ->rawColumns(['id', 'note', 'refer',]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(DuelEntry $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $id = decode_params($params);

        $model = $model->newQuery();
        //  $model->withTrashed();
        $model->leftJoin('duel_types', 'duel_entries.item_name', '=', 'duel_types.id');
        $model->leftJoin('projects', 'duel_entries.project_id', '=', 'projects.id');
        $model->select([
            'duel_entries.id',
            'duel_entries.ministry_id',
            'duel_types.name_km',
            'projects.company_name',
            'projects.stock_number',
            'projects.stock_name',
            'projects.user_entry',
            'duel_entries.unit',
            'duel_entries.quantity',
            'duel_entries.price',
            'duel_entries.total_price',
            'duel_entries.source',
            'duel_entries.pro_year',
            'projects.note',
            'projects.refer',
            'duel_entries.date_entry',
            'projects.title',
            'projects.file',
            'duel_entries.created_at',
            'duel_entries.updated_at',
        ])
            ->where('duel_entries.ministry_id', $id);

        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('duelentry-table')
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

            Column::make('stock_number')->title(__('tables.th.stock.number'))->width(60)->addClass('align-middle'),
            Column::make('user_entry')->title(__('tables.th.user.entry'))->width(80)->addClass('align-middle'),
            Column::make('name_km')->title(__('tables.th.item.name'))->width(90)->addClass('align-middle'),
            Column::make('quantity')->title(__('tables.th.quantity'))->addClass('align-middle'),
            Column::make('price')->title(__('tables.th.price'))->width(200)->addClass('align-middle'),
            Column::make('duel_total')->title(__('tables.th.duel.total'))->width(80)->addClass('align-middle'),
            Column::make('date_entry')->title(__('tables.th.date.entry'))->width(200)->addClass('align-middle'),

            Column::make('company_name')->title(__('tables.th.company.name'))->width(30)->addClass('align-middle'),
            Column::make('source')->title(__('tables.th.source'))->width(30)->addClass('align-middle'),
            Column::make('pro_year')->title(__('tables.th.pro_year'))->width(60)->addClass('align-middle'),
            // Column::computed('stock_name')->title(__('tables.th.stock.name'))->width(60)->addClass('align-middle'),
            // Column::computed('note')->title(__('tables.th.note'))->addClass('align-middle'),
            // Column::computed('refer')->title(__('tables.th.refer'))->width(200)->addClass('align-middle'),
            // Column::computed('file')->title(__('tables.th.file'))->width(200)->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)->printable(false)->width(100)->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'DuelEntry_' . date('YmdHis');
    }
}

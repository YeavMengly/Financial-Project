<?php

namespace App\DataTables\AnnualOpen;

use App\Models\BeginCredit\Ministry;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InitialProgramDataTable extends DataTable
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
            ->addColumn('action', function ($module) {
                return view('beginningcredit::program.initialProgram.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Ministry $model): QueryBuilder
    {
        $query = $model->newQuery()->select([
            'ministries.id',
            'ministries.no',
            'ministries.year',
            'ministries.title',
            'ministries.refer',
            'ministries.name'
        ]);

        return $query->orderBy('ministries.id', 'DESC');
    }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('initialprogram-table')
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
            Column::computed(
                'DT_RowIndex',
                __('tables.th.no')
            )->width(30)->addClass('text-center align-middle')->orderable(false),
            Column::make('year')->title(__('tables.th.year'))->width(80)->addClass('align-middle'),
            Column::make('title')->title(__('tables.th.title'))->addClass('align-middle'),
            Column::make('refer')->title(__('tables.th.refer'))->addClass('align-middle'),
            Column::make('name')->title(__('tables.th.description'))->addClass('align-middle'),
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
        return 'InitialProgram_' . date('YmdHis');
    }
}

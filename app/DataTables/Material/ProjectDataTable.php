<?php

namespace App\DataTables\Material;

use App\Models\Material\Projects;
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

class ProjectDataTable extends DataTable
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

            /*
        |--------------------------------------------------------------------------
        | soft_delete
        |--------------------------------------------------------------------------
        */

            ->editColumn('soft_delete', function ($soft_delete) {
                $active = (is_null($soft_delete->deleted_at)) ? '<span class="badge bg-success">' . __('buttons.active') . '</span>' : '<span class="badge bg-danger">' . __('buttons.deleted') . '</span>';
                return $active;
            })

            ->addColumn("dateTime", function ($module) {
                return Carbon::parse($module->created_at)->format('Y-m-d  h:i:s A');
            })

            /*
        |--------------------------------------------------------------------------
        | Date
        |--------------------------------------------------------------------------
        */
            ->editColumn('date', function ($row) {
                $active =  Carbon::parse($row->date)->format('Y-m-d');

                return $active;
            })

            /*
        |--------------------------------------------------------------------------
        | Title
        |--------------------------------------------------------------------------
        */
            ->editColumn('title', function ($row) {
                $title = e($row->title ?? '');

                if (empty($title)) {
                    return '<span class="text-muted">-</span>';
                }

                return '<strong>' . $title . '</strong>';
            })

            /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */
            ->addColumn('status', function ($row) {

                if (is_null($row->deleted_at)) {
                    return '<span class="badge bg-success">'
                        . __('buttons.active')
                        . '</span>';
                }

                return '<span class="badge bg-danger">'
                    . __('buttons.deleted')
                    . '</span>';
            })

            /*
        |--------------------------------------------------------------------------
        | Action
        |--------------------------------------------------------------------------
        */
            ->addColumn('action', function ($module) {
                return view(
                    'material::project.action',
                    [
                        'module' => $module,
                    ]
                );
            })

            /*
        |--------------------------------------------------------------------------
        | Note
        |--------------------------------------------------------------------------
        */
            ->editColumn('note', function ($row) {

                if (empty($row->note)) {
                    return '<span class="text-muted">-</span>';
                }

                return '<div
                        style="
                            max-height: 40px;
                            overflow-y: auto;
                            overflow-x: hidden;
                            white-space: normal;
                        "
                    >'
                    . nl2br(e($row->note))
                    . '</div>';
            })

            /*
        |--------------------------------------------------------------------------
        | Reference
        |--------------------------------------------------------------------------
        */
            ->editColumn('refer', function ($row) {

                if (empty($row->refer)) {
                    return '<span class="text-muted">-</span>';
                }

                return '<div
                        style="
                            max-height: 40px;
                            overflow-y: auto;
                            overflow-x: hidden;
                            white-space: normal;
                        "
                    >'
                    . nl2br(e($row->refer))
                    . '</div>';
            })

            /*
        |--------------------------------------------------------------------------
        | Files
        |--------------------------------------------------------------------------
        */
            ->editColumn('file', function ($row) {

                if (empty($row->file)) {
                    return '<span class="text-muted">-</span>';
                }

                /*
            |--------------------------------------------------------------------------
            | Decode JSON
            |--------------------------------------------------------------------------
            */
                $files = json_decode($row->file, true);

                /*
            |--------------------------------------------------------------------------
            | Multiple files
            |--------------------------------------------------------------------------
            */
                if (is_array($files) && count($files) > 0) {

                    $html = '<ul class="list-unstyled m-0">';

                    foreach ($files as $file) {

                        if (empty($file)) {
                            continue;
                        }

                        $file = (string) $file;

                        /*
                    |--------------------------------------------------------------------------
                    | Storage path
                    |--------------------------------------------------------------------------
                    |
                    | Your store() method uses:
                    |
                    | $file->store('materials/documents', 'public');
                    |
                    */
                        $url = asset(
                            'storage/' . ltrim($file, '/')
                        );

                        $filename = basename($file);

                        $html .= '
                        <li class="mb-1">
                            <a
                                href="' . e($url) . '"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-primary"
                            >
                                <i class="fas fa-file-alt me-1"></i>
                                ' . e($filename) . '
                            </a>
                        </li>
                    ';
                    }

                    $html .= '</ul>';

                    return $html;
                }

                /*
            |--------------------------------------------------------------------------
            | Single file
            |--------------------------------------------------------------------------
            */
                $file = (string) $row->file;

                $url = asset(
                    'storage/' . ltrim($file, '/')
                );

                $filename = basename($file);

                return '
                <a
                    href="' . e($url) . '"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-primary"
                >
                    <i class="fas fa-file-alt me-1"></i>
                    ' . e($filename) . '
                </a>
            ';
            })

            /*
        |--------------------------------------------------------------------------
        | Raw HTML columns
        |--------------------------------------------------------------------------
        */
            ->rawColumns([
                'title',
                'status',
                'action',
                'note',
                'refer',
                'file',
                'soft_delete'
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Projects $model, Request $request): QueryBuilder
    {
        /*
    |--------------------------------------------------------------------------
    | Decode ministry ID
    |--------------------------------------------------------------------------
    */
        $params = $request->params;
        $id = decode_params($params);

        /*
    |--------------------------------------------------------------------------
    | Get one project per stock_number
    |--------------------------------------------------------------------------
    */
        return $model->newQuery()
            ->select([
                'projects.id',
                'projects.ministry_id',
                'projects.stock_number',
                'projects.stock_name',
                'projects.company_name',
                'projects.warehouse_voucher',
                'projects.warehouse_owner',
                'projects.user_entry',
                'projects.user_receiver',
                'projects.date',
                'projects.title',
                'projects.file',
                'projects.note',
                'projects.refer',
                'projects.created_at',
                'projects.deleted_at',
            ])
            ->where('projects.ministry_id', $id)
            ->whereIn('projects.id', function ($query) use ($id) {
                $query->selectRaw('MIN(id)')
                    ->from('projects')
                    ->where('ministry_id', $id)
                    ->groupBy('stock_number');
            })
            ->orderBy('projects.stock_number', 'ASC');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('project-table')
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

            Column::make('stock_number')->title(__('tables.th.stock.number'))->width(30)->addClass('align-middle'),
            Column::make('stock_name')->title(__('tables.th.stock.name'))->width(30)->addClass('align-middle'),
            Column::make('company_name')->title(__('tables.th.company.name'))->width(90)->addClass('align-middle'),
            Column::make('warehouse_voucher')->title(__('tables.th.warehouse.voucher'))->width(90)->addClass('align-middle'),
            Column::make('user_entry')->title(__('tables.th.user.entry'))->width(60)->addClass('align-middle'),
            Column::make('user_receiver')->title(__('tables.th.receiver'))->width(60)->addClass('align-middle'),
            Column::make('warehouse_owner')->title(__('tables.th.warehouse.owner'))->width(90)->addClass('align-middle'),
            Column::computed('file')->title(__('tables.th.file'))->width(200)->addClass('align-middle'),
            Column::make('date')->title(__('tables.th.date.entry'))->width(200)->addClass('align-middle'),
            Column::make('title')->title(__('tables.th.title'))->width(80)->addClass('align-middle'),
            Column::make('note')->title(__('tables.th.note'))->addClass('align-middle'),
            Column::make('refer')->title(__('tables.th.refer'))->addClass('align-middle'),
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
        return 'Project_' . date('YmdHis');
    }
}

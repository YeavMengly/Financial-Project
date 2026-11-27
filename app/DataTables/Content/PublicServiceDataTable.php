<?php

namespace App\DataTables\Content;

use App\Models\Content\PublicService;
use App\Models\Revenue\PublicServiceCategory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicServiceDataTable extends DataTable
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
            ->addColumn('service_name', function ($module) {
                return '<strong>'
                    . e($module->service_prefix_name)
                    . '</strong><br/><hr/>'
                    . e($module->service_name);
            })
            ->addColumn('category', function ($row) {
                return $row->category_code . ' - ' . $row->service_cate_name;
            })
            ->editColumn('service_price', function ($row) {
                return number_format($row->service_price) . ' ៛';
            })
            ->editColumn('max_service_time', function ($row) {
                return number_format($row->max_service_time) . 'ថ្ងៃ';
            })
            ->editColumn('percentage_national', function ($row) {
                return number_format($row->percentage_national, 2) . '%';
            })
            ->editColumn('percentage_mlvt', function ($row) {
                return number_format($row->percentage_mlvt, 2) . '%';
            })
            ->editColumn('percentage_interior', function ($row) {
                return number_format($row->percentage_interior, 2) . '%';
            })
            ->editColumn('percentage_mef', function ($row) {
                return number_format($row->percentage_mef, 2) . '%';
            })
            ->addColumn('action', function ($module) {

                return view('content::content.publicServices.action', ['module' => $module]);
            })
            // ->editColumn('expired', function ($row) {
            //     return $row->expired == 0
            //         ? '<span class="badge bg-danger">Expired</span>'
            //         : '<span class="badge bg-success">Active</span>';
            // })
            ->rawColumns(['soft_delete', 'action', 'expired', 'service_name'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PublicService $model, Request $request): QueryBuilder
    {
        // return $model->newQuery();

        $model = $model->newQuery();
        // $model->withTrashed();


        // // ===== Filter Employee =====
        if ($request->filled('cboCate')) {

            $categoryId = $request->cboCate;

            $categoryIds = PublicServiceCategory::where('id', $categoryId)
                ->orWhere('parent', $categoryId)
                ->pluck('id');

            $model->whereIn(
                'public_service_categories.id',
                $categoryIds
            );
        }

        $query = $model->newQuery()
            ->leftJoin(
                'public_service_categories',
                'public_services.categories_id',
                '=',
                'public_service_categories.id'
            )
            ->select([
                'public_services.id',
                'public_services.categories_id',

                // Service information
                'public_services.service_code',
                'public_services.service_prefix_name',
                'public_services.service_name',
                'public_services.service_price',
                'public_services.max_service_time',
                'public_services.expired',

                // Revenue distribution
                'public_services.percentage_national',
                'public_services.percentage_mlvt',
                'public_services.percentage_interior',
                'public_services.percentage_mef',

                // Category information
                'public_service_categories.code as category_code',
                'public_service_categories.service_cate_name',

                // Timestamps
                'public_services.created_at',
                'public_services.updated_at',
                // 'public_services.deleted_at',
            ])
            ->orderBy('public_services.id', 'ASC');

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('publicservice-table')
            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])
            ->ajax([
                'data' => 'function(d) {
                d.cboCate = $("#cboCate").val();
            }',
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
                ->title(__('tables.th.no'))
                ->width(40)
                ->addClass('text-center align-middle')
                ->orderable(false)
                ->searchable(false),

            Column::make('category')
                ->title(__('forms.public.service.agency'))
                ->addClass('align-middle'),

            Column::make('service_code')
                ->title(__('forms.service.code'))
                ->addClass('align-middle'),

            // Column::make('service_prefix_name')
            //     ->title('បុព្វបទឈ្មោះសេវា')
            //     ->addClass('align-middle'),

            Column::make('service_name')
                ->title(__('forms.service.name'))
                ->addClass('align-middle'),

            Column::make('service_price')
                ->title(__('forms.service.price'))
                ->width(120)
                ->addClass('text-end align-middle'),

            Column::make('max_service_time')
                ->title(__('forms.max.service.time'))
                ->width(120)
                ->addClass('text-center align-middle'),

            Column::make('expired')
                ->title(__('forms.expired'))
                ->width(100)
                ->addClass('text-center align-middle'),

            Column::make('percentage_national')
                ->title(__('forms.percentage.national'))
                ->width(100)
                ->addClass('text-center align-middle'),

            Column::make('percentage_mlvt')
                ->title(__('forms.percentage.mlvt'))
                ->width(100)
                ->addClass('text-center align-middle'),

            Column::make('percentage_interior')
                ->title(__('forms.percentage.interior'))
                ->width(100)
                ->addClass('text-center align-middle'),

            Column::make('percentage_mef')
                ->title(__('forms.percentage.mef'))
                ->width(100)
                ->addClass('text-center align-middle'),

            Column::make('created_at')
                ->title(__('tables.th.createdAt'))
                ->width(160)
                ->addClass('text-center align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center align-middle'),
        ];
    }


    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'PublicService_' . date('YmdHis');
    }
}

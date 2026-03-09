<?php

namespace App\DataTables\Content;

use App\Models\Content\Account;
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

class AccountDataTable extends DataTable
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
                return view('content::content.accounts.action', ['module' => $module]);
            })
            ->rawColumns(['soft_delete', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Account $model, Request $request): QueryBuilder
    {
        $params = $request->params;
        $chId = $request->chId;

        $id = decode_params($params);
        $chId = decode_params($chId);

        $model = $model->newQuery();
        $model->withTrashed();
        $query = $model->newQuery()
            ->leftJoin('chapters', 'accounts.chapter_id', '=', 'chapters.id')
            ->select([
                'accounts.id',
                'accounts.ministry_id',
                'accounts.chapter_id',
                'accounts.no',
                'accounts.name',
                'accounts.created_at',
                'accounts.deleted_at'
            ])
            ->where('accounts.ministry_id', $id)
            ->where('accounts.chapter_id', $chId)
            ->orderBy('accounts.created_at', 'DESC');
        // $query->where('accounts.ministry_id', $id);
        // $query->where('accounts.chapter_id', $chId);

        if ($request->filled('no')) {
            $query->where('accounts.id', $request->no);
        }
        if ($request->filled('name')) {
            $query->where('accounts.name', 'like', '%' . $request->name . '%');
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('account-table')
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

            Column::make('no')->title(__('tables.th.account'))->addClass('align-middle'),
            Column::make('name')->title(__('tables.th.name'))->addClass('align-middle'),
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
        return 'Account_' . date('YmdHis');
    }
}

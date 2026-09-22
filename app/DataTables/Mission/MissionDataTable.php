<?php

namespace App\DataTables\Mission;

use App\Models\Mission\Mission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class MissionDataTable extends DataTable
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
            ->addColumn('select', function ($row) {
                // Already paid
                if ((int) $row->payment_is_archived === 2) {
                    return '';
                }
                // Not paid yet
                return '
                    <input type="checkbox" class="form-check-input mission-checkbox" value="' . e($row->id) . '">
                ';
            })
            ->editColumn('soft_delete', function ($row) {

                $active = is_null($row->deleted_at)
                    ? '<span class="badge bg-success">'
                    . __('buttons.active') .
                    '</span>'
                    : '<span class="badge bg-danger">'
                    . __('buttons.deleted') .
                    '</span>';

                return $active;
            })
            ->addColumn('dateTime', function ($module) {
                return Carbon::parse($module->created_at)
                    ->format('Y-m-d h:i:s A');
            })
            ->addColumn('description', function ($module) {
                return '<strong>'
                    . e($module->description) ?? '-'
                    . '</strong><br/><hr/>';
            })
            ->addColumn('days_count', function ($module) {
                return '<strong>'
                    . e($module->days_count)
                    . 'ថ្ងៃ</strong><br/><hr/>'
                    . e($module->nights_count)
                    . 'យប់';
            })
            ->editColumn('payment_status', function ($row) {

                return (int) $row->payment_is_archived === 1
                    ? 'មិនទាន់ទូទាត់'
                    : 'បានទូទាត់រួចរាល់';
            })
            ->editColumn('payment_is_archived', function ($row) {

                $selectedTodo = (int) $row->payment_is_archived === 1
                    ? 'selected'
                    : '';

                $selectedDone = (int) $row->payment_is_archived === 2
                    ? 'selected'
                    : '';

                return "
                <select
                    class='form-select form-select-sm payment-status'
                    data-id='{$row->id}'
                    style='min-width: 150px;'
                >
                    <option value='1' {$selectedTodo}>
                        មិនទាន់បង់
                    </option>

                    <option value='2' {$selectedDone}>
                        បានបង់
                    </option>
                </select>
            ";
            })
            ->editColumn('mission_type', function ($row) {

                return (int) $row->mission_type_is_archived === 1
                    ? 'ក្នុងប្រទេស'
                    : 'ក្រៅប្រទេស';
            })
            ->addColumn('action', function ($module) {
                return view('mission::missions.action', [
                    'module' => $module,
                ]);
            })
            ->editColumn('legal_date', function ($row) {
                return $row->legal_date
                    ? Carbon::parse($row->legal_date)->format('Y-m-d')
                    : '-';
            })
            ->editColumn('start_date', function ($row) {
                return $row->start_date
                    ? Carbon::parse($row->start_date)->format('Y-m-d')
                    : '-';
            })
            ->editColumn('end_date', function ($row) {
                return $row->end_date
                    ? Carbon::parse($row->end_date)->format('Y-m-d')
                    : '-';
            })
            ->editColumn('fileName', function ($row) {
                if (!$row->fileName) {
                    return '<span class="text-muted">-</span>';
                }
                $url = asset('storage/' . $row->fileName);
                $filename = basename($row->fileName);

                return "<a href='{$url}' target='_blank' class='text-primary'>
                <i class='fas fa-file-alt me-1'></i>Preview
            </a>";
            })
            ->rawColumns([
                'select',
                'description',
                'soft_delete',
                'days_count',
                'payment_is_archived',
                'legal_number',
                'fileName',
                'action'
            ]);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Mission $model, Request $request): QueryBuilder
    {
        // ===== Fetch Parameters =====
        $params = request()->params;
        $id = decode_params($params);

        $model = $model->newQuery();

        // ===== Join Tables =====

        $model->leftJoin('ministries', 'missions.ministry_id', '=', 'ministries.id');
        $model->leftJoin('provinces', 'missions.province_id', '=', 'provinces.id');
        $model->leftJoin('documents', 'missions.document_id', '=', 'documents.id');

        $model->leftJoin('mission_employees', 'missions.id', '=', 'mission_employees.mission_id');

        if ($request->cboTodo) {
            if ($request->cboTodo == 2) {
                $model->where('missions.payment_is_archived', 1);
            } elseif ($request->cboTodo == 3) {
                $model->where('missions.payment_is_archived', 2);
            }
        } else {
            $model->where('missions.payment_is_archived', 1);
        }

        // ===== Filter Mission Type =====

        $missionType = $request->input('cboMissionType', 2);
        if ($missionType == 2) {

            // ក្នុងប្រទេស
            $model->where(
                'missions.mission_type_is_archived',
                1
            );
        } elseif ($missionType == 3) {

            // ក្រៅប្រទេស
            $model->where(
                'missions.mission_type_is_archived',
                2
            );
        }

        // // ===== Filter Employee =====
        if ($request->filled('cboName')) {

            $model->where(
                'missions.leader_id',
                $request->cboName
            );
        }


        // // ===== Filter Province =====

        if ($request->filled('cboProvince')) {

            $model->where(
                'missions.province_id',
                $request->cboProvince
            );
        }

        // // ===== Filter Legal Date =====

        if ($request->filled('start_date')) {

            $model->whereDate(
                'missions.start_date',
                '>=',
                $request->start_date
            );
        }

        if ($request->filled('end_date')) {

            $model->whereDate(
                'missions.end_date',
                '<=',
                $request->end_date
            );
        }

        // ===== Ministry Filter =====

        $model->where(
            'missions.ministry_id',
            $id
        );

        // ===== Select Columns =====

        $model->select([
            // Main mission
            'missions.id',
            'missions.ministry_id',
            'missions.document_id',
            'missions.leader_id',
            'missions.province_id',
            'missions.legal_number',
            'missions.legal_date',
            'missions.description',
            'missions.start_date',
            'missions.end_date',
            'missions.days_count',
            'missions.nights_count',
            'missions.mission_type',
            'missions.mission_type_is_archived',
            'missions.fileName',
            'missions.payment_status',
            'missions.payment_is_archived',
            'documents.name as doc_name',

            // Province
            'provinces.name as province_name',

            // Timestamps
            'missions.created_at',
            'missions.deleted_at',


            // Count employees in this mission
            DB::raw('COUNT(mission_employees.id) as employee_count'),

        ]);

        // ===== Group By =====

        $model->groupBy([
            'missions.id',
            'missions.ministry_id',
            'missions.document_id',
            'missions.leader_id',
            'missions.province_id',
            'missions.legal_number',
            'missions.legal_date',
            'missions.description',
            'missions.start_date',
            'missions.end_date',
            'missions.days_count',
            'missions.nights_count',
            'missions.mission_type',
            'missions.mission_type_is_archived',
            'missions.fileName',
            'missions.payment_status',
            'missions.payment_is_archived',
            'documents.name',
            'provinces.name',
            'missions.created_at',
            'missions.deleted_at',
        ]);

        // ===== Order =====

        $model->orderBy(
            'missions.created_at',
            'asc'
        );

        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()

            ->setTableId('mission-table')

            ->parameters([
                'language' => [
                    'url' => asset('assets/lang/language.json'),
                ],
            ])

            ->ajax([
                'data' => 'function(d) {

                d.cboTodo        = $("#cboTodo").val();
                d.cboStatus      = $("#cboStatus").val();
                d.cboMissionType = $("#cboMissionType").val();
                d.cboName        = $("#cboName").val();
                d.cboPosition    = $("#cboPosition").val();
                d.cboLevel       = $("#cboLevel").val();
                d.cboProvince    = $("#cboProvince").val();
                d.start_date     = $("#start_date").val();
                d.end_date       = $("#end_date").val();

            }',
            ])

            ->columns($this->getColumns())

            ->orderBy(2, 'ASC')

            ->drawCallback('function () {

            function updatePaymentButton() {

                let checked = $(".mission-checkbox:checked").length;

                $("#btnPaymentStatus").prop(
                    "disabled",
                    checked === 0
                );
            }

            // Check all
            $("#checkAllMissions")
                .off("change")
                .on("change", function () {

                    let isChecked = $(this).is(":checked");

                    $(".mission-checkbox").prop(
                        "checked",
                        isChecked
                    );

                    updatePaymentButton();
                });

            // Individual checkbox
            $(".mission-checkbox")
                .off("change")
                .on("change", function () {

                    let total = $(".mission-checkbox").length;

                    let checked =
                        $(".mission-checkbox:checked").length;

                    $("#checkAllMissions").prop(
                        "checked",
                        total > 0 && total === checked
                    );

                    updatePaymentButton();
                });

            // Update button after every Ajax redraw
            updatePaymentButton();
        }');
    }
    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title(__('tables.th.no'))
                ->width(50)
                ->addClass('text-center align-middle')
                ->orderable(false)
                ->searchable(false),

            Column::computed('select')
                ->title('
                <input
                    type="checkbox"
                    class="form-check-input"
                    id="checkAllMissions"
                >')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-center align-middle')
                ->width(50),

            Column::computed('payment_status')
                ->title(__('Task'))
                ->width(60)
                ->addClass('text-center align-middle'),

            Column::make('legal_number')
                ->title(__('tables.th.legal.number'))
                ->width(60)
                ->addClass('align-middle'),

            Column::make('legal_date')
                ->title(__('tables.th.date.legal'))
                ->width(120)
                ->addClass('align-middle'),

            Column::make('province_name')
                ->title(__('tables.th.province'))
                ->width(150)
                ->addClass('align-middle'),

            Column::make('employee_count')
                ->title(__('tables.th.mission.employee.count'))
                ->width(150)
                ->addClass('align-middle'),

            Column::make('start_date')
                ->title(__('tables.th.start.date'))
                ->width(120)
                ->addClass('align-middle'),

            Column::make('end_date')
                ->title(__('tables.th.end.date'))
                ->width(120)
                ->addClass('align-middle'),

            Column::make('days_count')
                ->title(__('tables.th.time.date'))
                ->width(100)
                ->addClass('text-center align-middle'),

            Column::make('mission_type')
                ->title(__('tables.th.mission.type'))
                ->width(120)
                ->addClass('align-middle'),

            Column::make('description')
                ->title(__('tables.th.mission.description'))
                ->addClass('align-middle'),

            Column::make('dateTime')
                ->title(__('tables.th.createdAt'))
                ->width(180)
                ->addClass('align-middle'),

            Column::make('doc_name')
                ->title(__('tables.th.mission.type'))
                ->width(180)
                ->addClass('align-middle'),

            Column::make('fileName')
                ->title(__('tables.th.file'))
                ->width(60)
                ->addClass('align-middle'),

            Column::computed('action', __('tables.th.action'))
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Mission_' . date('YmdHis');
    }
}

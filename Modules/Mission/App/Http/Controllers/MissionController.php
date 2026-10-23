<?php

namespace Modules\Mission\App\Http\Controllers;

use App\DataTables\Mission\InitialMissionDataTable;
use App\DataTables\Mission\MissionDataTable;
use App\Exports\Mission\MissionExport;
use App\Http\Controllers\Controller;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\Content\Cluster;
use App\Models\Content\Levels;
use App\Models\Content\Ministry;
use App\Models\Content\Employee;
use App\Models\Content\Positions;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use App\Models\Document;
use App\Models\Mission\Mission;
use App\Models\Mission\MissionEmployee;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MissionController extends Controller
{

    public function getIndex(InitialMissionDataTable $dataTable)
    {
        // return view('maintenance.maintenance');
        return $dataTable->render('mission::missions.initialMission.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MissionDataTable $dataTable, $params)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();
        $position = Positions::all();
        $level = Levels::all();
        $provinces = Province::all();
        $employees = Employee::whereNotNull('id_number')->orderBy('id')->get();

        // $paymentDrafts = Mission::where('payment_approval_status', 'draft')
        //     ->get();

        // $approvalRecords = Mission::whereIn('payment_approval_status', [
        //     'pending',
        //     'approved',
        //     'rejected',
        //     'paid',
        // ])->get();
        $pendingMissionCount = Mission::where('payment_is_archived', 2)->count();

        $exportLogs = Mission::query()
            ->where('ministry_id', decode_params($params))
            ->where('payment_status', 'paid')
            ->whereNull('deleted_at')
            ->select('paid_at')
            ->distinct()
            ->orderByDesc('paid_at')
            ->get();


        return $dataTable->render('mission::missions.index', [
            'params' => $params,
            'ministry' => $ministry,
            'position' => $position,
            'level' => $level,
            'provinces' => $provinces,
            'employees' => $employees,
            // 'paymentDrafts' => $paymentDrafts,
            // 'approvalRecords' => $approvalRecords,

            'pendingMissionCount' => $pendingMissionCount,
            'exportLogs' => $exportLogs,
        ]);
    }

    public function getByLevel(Request $request)
    {
        if ($request->position_id) {
            $data = Levels::select('id', 'position_id', 'name')
                ->where('id', $request->position_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = $selectedId == $d->id ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->name}</option>";
            }

            return response($html);
        }

        return response('');
    }

    public function getByPositionLevel(Request $request)
    {
        $request->validate([
            'level_id' => 'required|integer',
        ]);

        $levels = Levels::where('id', $request->level_id)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return response()->json($levels);
    }

    /**
     * AJAX: Fetch program sub-options by program ID request.
     */
    public function getByProgramId(Request $request)
    {
        if ($request->program_id) {
            $data = ProgramSub::select('id', 'program_id', 'no', 'decription')
                ->where('program_id', $request->program_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = $selectedId == $d->id ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
            }

            return response($html);
        }

        return response('');
    }

    public function editByProgramId(Request $request)
    {
        if (!$request->program_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = ProgramSub::select('id', 'no', 'decription')
            ->where('program_id', $request->program_id)
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
        }

        return response($html);
    }

    public function getByAgency(Request $request)
    {
        if ($request->program_id) {
            $data = Agency::select('id', 'program_id', 'no', 'name')
                ->where('program_id', $request->program_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = $selectedId == $d->id ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->name}</option>";
            }

            return response($html);
        }

        return response('');
    }

    public function editByAgency(Request $request)
    {
        if (!$request->program_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = Agency::select('id', 'no', 'name')
            ->where('program_id', $request->program_id)
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->name}</option>";
        }

        return response($html);
    }

    public function getByProgramSubId(Request $request)
    {
        if ($request->program_sub_id) {

            $data = Cluster::select('id', 'program_sub_id', 'no', 'decription')
                ->where('program_sub_id', $request->program_sub_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = ((string)$selectedId === (string)$d->id) ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
            }

            return response($html);
        }

        return response('');
    }

    public function editByProgramSubId(Request $request)
    {
        if (!$request->program_sub_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = Cluster::select('id', 'no', 'decription')
            ->where('program_sub_id', $request->program_sub_id)
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
        }

        return response($html);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $positions = Positions::all();
        $provinces = Province::all();
        // $employees = Employee::whereNotNull('id_number')->orderBy('id')->get();
        $employees = Employee::whereNotNull('name_kh')
            ->whereNotNull('account_number')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MIN(id)')
                    ->from('employees')
                    // ->whereNotNull('id_number')
                    ->groupBy('name_kh', 'account_number');
            })
            ->orderBy('id')
            ->get();
        $document = Document::all();
        $program   = Program::where('ministry_id', $ministry->id)->orderBy('no')->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();


        return view('mission::missions.create')
            ->with('ministry', $ministry)
            ->with('positions', $positions)
            ->with('provinces', $provinces)
            ->with('employees', $employees)
            ->with('params', $params)
            ->with('document', $document)
            ->with('program', $program)
            ->with('accountSub', $accountSub);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'cboDocument' => 'required',

            'legal_number'     => 'required|string',
            'legal_date'  => 'required|date',

            'cboTitle'    => 'required|array',
            'cboTitle.*'  => 'required|string',

            'cboName'             => 'required|array|min:1',
            'cboName.*'             => 'required|integer|exists:employees,id',

            'cboPosition'             => 'required|array|min:1',
            'cboPosition.*'             => 'required|integer|exists:positions,id',

            'cboProvince'        => 'required|integer|exists:provinces,id',

            'start_date'  => 'required|date',
            'end_date'  => 'required|date|after_or_equal:start_date',

            'txtDescription' => 'required|string|max:9999',

            'leader_index' =>  'required',
            // Assign checkbox
            'assign_budget' => 'required|array',
            'assign_budget.*' => 'required|integer',

            'fileName' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240', // 10MB in Kilobytes (10 * 1024)
            ],

            'cboProgram'     => 'nullable|integer',
            'cboProgramSub'  => 'nullable|integer',
            'cboCluster'     => 'nullable|integer',
            // 'cboAgency'           => 'nullable|integer',
            'cboSubAccount'       => 'nullable|integer',
            'account_number' => ['required', 'array'],
            'account_number.*' => ['required', 'string'],
            'payment_account_employee_id' => ['required', 'array'],
            'payment_account_employee_id.*' => [
                'required',
                'integer',
                'exists:employees,id',
            ],
        ]);
        // dd($validated);
        $id = decode_params($params);
        DB::beginTransaction();
        try {

            /*
            |--------------------------------------------------------------------------
            | Get Ministry
            |--------------------------------------------------------------------------
            */
            $ministry   = Ministry::where('id', $id)->first();
            /*
            |--------------------------------------------------------------------------
            | Calculate days / nights
            |--------------------------------------------------------------------------
            */
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);

            $daysCount = $startDate->diffInDays($endDate) + 1;
            $nightsCount = max($daysCount - 1, 0);

            $province = Province::where('id', $validated['cboProvince'])->first();
            $accountSub = AccountSub::where('ministry_id', $ministry->id)->first();

            /*
            |--------------------------------------------------------------------------
            | Create Mission for each employee
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | 2. Store Table 1: missions
            |--------------------------------------------------------------------------
            */
            // dd($validated['legal_type']);
            $is_archived = 1;/*
            |--------------------------------------------------------------------------
            | Get employee IDs
            |--------------------------------------------------------------------------
            */
            $employeeIds = $request->input('cboName', []);


            /*
            |--------------------------------------------------------------------------
            | Get selected leader row index
            |--------------------------------------------------------------------------
            */
            $leaderIndex = (int) $request->input('leader_index', 0);


            /*
            |--------------------------------------------------------------------------
            | Get leader employee ID from cboName[]
            |--------------------------------------------------------------------------
            */
            $leaderEmployeeId = $employeeIds[$leaderIndex] ?? null;

            $mission = Mission::create([
                'leader_id' => $leaderEmployeeId,
                'ministry_id' => $ministry->id,
                'payment_status' => 'unpaid',
                'payment_is_archived' => 1,
                'legal_number' => $validated['legal_number'],
                'legal_date' => $validated['legal_date'],
                'description' => strip_tags($validated['txtDescription']),
                'province_id' => $validated['cboProvince'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'days_count' => $daysCount,
                'nights_count' => $nightsCount,
                'mission_type' => $is_archived == 1 ? 'local' : 'abroad',
                'mission_type_is_archived' => $is_archived,
                'document_id' => $validated['cboDocument'],
                'fileName' => $validated['fileName'] ?? null,
                'program_id'          => $validated['cboProgram'],
                'program_sub_id'      => $validated['cboProgramSub'],
                'cluster_id'          => $validated['cboCluster'],
                'chapter_id'     => substr($accountSub->no, 0, 2),
                'account_id'     => substr($accountSub->no, 0, 4),
                'account_sub_id' => $accountSub->no,
            ]);

            foreach ($validated['cboName'] as $index => $employeeId) {

                $positionId = $validated['cboPosition'][$index] ?? null;

                $assignBudget = (int) (
                    $validated['assign_budget'][$index] ?? 0
                );

                // Employee's own account
                $accountNumber =
                    $validated['account_number'][$index] ?? null;

                // Employee who should receive the payment
                $paymentAccountEmployeeId =
                    $validated['payment_account_employee_id'][$index] ?? null;
                /*
                |--------------------------------------------------------------------------
                | Payment employee
                |--------------------------------------------------------------------------
                */
                $paymentEmployee = Employee::findOrFail(
                    $paymentAccountEmployeeId
                );

                // Get their account number
                $paymentAccountNumber = $paymentEmployee->account_number;

                /*
                |--------------------------------------------------------------------------
                | Position
                |--------------------------------------------------------------------------
                */
                $position = Positions::findOrFail($positionId);


                /*
                |--------------------------------------------------------------------------
                | Level
                |--------------------------------------------------------------------------
                */
                $level = Levels::findOrFail($position->level_id);


                /*
                |--------------------------------------------------------------------------
                | Employee
                |--------------------------------------------------------------------------
                */
                $employee = Employee::findOrFail($employeeId);

                $employeeTitle = $validated['cboTitle'][$index] ?? null;

                /*
                |--------------------------------------------------------------------------
                | Money calculation
                |--------------------------------------------------------------------------
                */
                $pocketMoney = $level->pocket_money;
                $totalPocketMoney = $pocketMoney * $daysCount;

                $mealMoney = $level->meal_money;
                $totalMealMoney = $mealMoney * $daysCount;

                $accommodationMoney = $level->accommodation_money;
                $totalAccommodationMoney =
                    $accommodationMoney * $nightsCount;


                /*
                |--------------------------------------------------------------------------
                | Condition assign budget
                |--------------------------------------------------------------------------
                */
                if ($assignBudget === 1) {

                    $travelAllowance =
                        $province->budget ?? 0;

                    $total =
                        $travelAllowance +
                        $totalPocketMoney +
                        $totalMealMoney +
                        $totalAccommodationMoney;
                } else {

                    $travelAllowance = 0;

                    $total =
                        $totalPocketMoney +
                        $totalMealMoney +
                        $totalAccommodationMoney;
                }


                /*
                |--------------------------------------------------------------------------
                | Check monthly mission days
                |--------------------------------------------------------------------------
                */
                $usedDays = Mission::whereHas(
                    'missionEmployees',
                    function ($query) use ($employeeId) {

                        $query->where(
                            'employee_id',
                            $employeeId
                        );
                    }
                )
                    ->where(function ($query) use ($startDate) {

                        $query
                            ->whereYear(
                                'start_date',
                                $startDate->year
                            )
                            ->whereMonth(
                                'start_date',
                                $startDate->month
                            );
                    })
                    ->sum('days_count');


                $totalDays =
                    $usedDays + $daysCount;


                if ($totalDays > 10) {

                    throw new \Exception(
                        'បុគ្គលិក ' .
                            ($employee->name_kh ?? $employeeId) .
                            ' បានប្រើប្រាស់ថ្ងៃបេសកកម្ម ' .
                            $usedDays .
                            ' ថ្ងៃក្នុងខែនេះ។ ' .
                            'មិនអាចបញ្ចូលបន្ថែម ' .
                            $daysCount .
                            ' ថ្ងៃបានទេ ព្រោះអតិបរមា 10 ថ្ងៃក្នុងមួយខែ។'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Create Mission Employee
                |--------------------------------------------------------------------------
                */
                MissionEmployee::create([

                    'ministry_id' =>
                    $ministry->id,

                    'mission_id' =>
                    $mission->id,

                    'employee_title' => $employeeTitle,

                    'employee_id' =>
                    $employeeId,

                    'position_id' =>
                    $positionId,

                    'level_name' =>
                    $level->name,

                    'travel_allowance' =>
                    $travelAllowance,

                    'pocket_money' =>
                    $pocketMoney,

                    'total_pocket_money' =>
                    $totalPocketMoney,

                    'meal_money' =>
                    $mealMoney,

                    'total_meal_money' =>
                    $totalMealMoney,

                    'accommodation_money' =>
                    $accommodationMoney,

                    'total_accommodation_money' =>
                    $totalAccommodationMoney,

                    'total' =>
                    $total,

                    'assign_budget' =>
                    $assignBudget,

                    /*
                    |--------------------------------------------------------------------------
                    | Employee's OWN account
                    |--------------------------------------------------------------------------
                    */
                    'account_number' =>
                    $accountNumber,

                    /*
                    |--------------------------------------------------------------------------
                    | Employee who RECEIVES payment
                    |--------------------------------------------------------------------------
                    */
                    // Employee ID receiving payment
                    'payment_account_employee_id' => $paymentAccountEmployeeId,

                    // If you have a column for the payment account number:
                    'payment_account_number' => $paymentAccountNumber,
                ]);
            }
            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->has('submit')) {
                return redirect()->route('missions.index', $params);
            }

            return redirect()->route('missions.create', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('missions.index', $params);
        }
    }

    public function show($params, $id): View
    {
        $ministryId = decode_params($params);
        $missionId = decode_params($id);

        $mission = DB::table('missions')
            ->leftJoin(
                'mission_employees',
                'missions.id',
                '=',
                'mission_employees.mission_id'
            )
            ->leftJoin(
                'employees',
                'mission_employees.employee_id',
                '=',
                'employees.id'
            )
            ->leftJoin(
                'positions',
                'mission_employees.position_id',
                '=',
                'positions.id'
            )
            ->leftJoin(
                'provinces',
                'missions.province_id',
                '=',
                'provinces.id'
            )
            ->where('missions.id', $missionId)
            ->where('missions.ministry_id', $ministryId)
            ->select([
                'missions.*',

                'provinces.name as province_name',
                'mission_employees.employee_title',
                'mission_employees.employee_id as mission_employee_id',
                'mission_employees.travel_allowance',
                'mission_employees.pocket_money',
                'mission_employees.total_pocket_money',
                'mission_employees.meal_money',
                'mission_employees.total_meal_money',
                'mission_employees.accommodation_money',
                'mission_employees.total_accommodation_money',
                'mission_employees.assign_budget',
                'mission_employees.total',
                'mission_employees.account_number',

                'employees.id as employee_id',

                'employees.name_kh',
                'employees.name_latin',
                'employees.account_number as payment_account_employee_id',
                'employees.id_number',

                'mission_employees.level_name',

                'positions.name',
            ])
            ->get();


        if ($mission->isEmpty()) {
            abort(404);
        }

        return view('mission::missions.show', [
            'params' => $params,
            'mission' => $mission,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();

        $mission = Mission::where('id', decode_params($id))
            ->where('ministry_id', $ministry->id)
            ->where('payment_is_archived', 1)
            ->first();
        // Return warning flash if voucher record is not found or processed
        if (!$mission) {
            flash()->translate('en')->option('timeout', 2000)
                ->warning('ទិន្ន័យបានបញ្ចប់', 'Task')->flash();
            return back()->withInput();
        }
        $missionEmployees = MissionEmployee::with('employee')
            ->where('mission_id', $mission->id)
            ->get();

        $positions = Positions::all();

        $provinces = Province::all();

        $employees = Employee::whereNotNull('name_kh')
            ->whereNotNull('account_number')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MIN(id)')
                    ->from('employees')
                    ->whereNotNull('name_kh')
                    ->whereNotNull('account_number')
                    ->groupBy('name_kh', 'account_number');
            })
            ->orderBy('id')
            ->get();

        $document = Document::all();

        $program = Program::where('ministry_id', $ministry->id)
            ->orderBy('no')
            ->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();

        return view('mission::missions.edit')
            ->with('ministry', $ministry)
            ->with('mission', $mission)
            ->with('positions', $positions)
            ->with('provinces', $provinces)
            ->with('employees', $employees)
            ->with('params', $params)
            ->with('document', $document)
            ->with('program', $program)
            ->with('accountSub', $accountSub)
            ->with('missionEmployees', $missionEmployees);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'cboDocument' => 'required|integer|exists:documents,id',

            'legal_number' => 'required|string',

            'legal_date' => 'required|date',

            'cboProvince' => 'required|integer|exists:provinces,id',

            'start_date' => 'required|date',

            'end_date' => 'required|date|after_or_equal:start_date',

            'txtDescription' => 'required|string|max:9999',

            'cboName' => 'required|array|min:1',
            'cboName.*' => 'required|integer|exists:employees,id',

            'cboPosition' => 'required|array|min:1',
            'cboPosition.*' => 'required|integer|exists:positions,id',

            'leader_index' => 'required|integer|min:0',

            'assign_budget' => 'nullable|array',
            'assign_budget.*' => 'nullable|integer|in:0,1',

            'fileName' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:10240',
            ],

            'cboProgram' => 'nullable|integer',
            'cboProgramSub' => 'nullable|integer',
            'cboCluster' => 'nullable|integer',
            'cboSubAccount'       => 'nullable|integer',
            'account_number' => ['required', 'array'],
            'account_number.*' => ['required', 'string'],
            'payment_account_employee_id' => ['required', 'array'],
            'payment_account_employee_id.*' => [
                'required',
                'integer',
                'exists:employees,id',
            ],

            'submit' => 'required|in:save,save_create',
        ]);
        // dd($validated);
        DB::beginTransaction();

        try {

            // ---------------------------------------------------------
            // Ministry
            // ---------------------------------------------------------

            $ministry = Ministry::where('id', decode_params($params))
                ->firstOrFail();
            // ---------------------------------------------------------
            // Mission
            // ---------------------------------------------------------

            $mission = Mission::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            // ---------------------------------------------------------
            // Dates
            // ---------------------------------------------------------

            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);

            $daysCount = $startDate->diffInDays($endDate) + 1;

            $nightsCount = max($daysCount - 1, 0);

            // ---------------------------------------------------------
            // Province
            // ---------------------------------------------------------

            $province = Province::findOrFail(
                $validated['cboProvince']
            );

            // ---------------------------------------------------------
            // Leader
            // ---------------------------------------------------------

            $employeeIds = $validated['cboName'];

            $leaderIndex = (int) $validated['leader_index'];

            $leaderEmployeeId = $employeeIds[$leaderIndex] ?? null;

            if (!$leaderEmployeeId) {
                throw new \Exception('Leader employee is required.');
            }

            // ---------------------------------------------------------
            // File
            // ---------------------------------------------------------

            $fileName = $mission->fileName;

            if ($request->hasFile('fileName')) {

                $file = $request->file('fileName');

                $folder = 'uploads/mission/' . now()->format('Y-m-d');

                $fileName = $file->store(
                    $folder,
                    'public'
                );
            }

            // ---------------------------------------------------------
            // Update Mission
            // ---------------------------------------------------------

            $mission->update([
                'leader_id' => $leaderEmployeeId,

                'legal_number' => $validated['legal_number'],

                'legal_date' => $validated['legal_date'],

                'description' => strip_tags(
                    $validated['txtDescription']
                ),

                'province_id' => $validated['cboProvince'],

                'start_date' => $validated['start_date'],

                'end_date' => $validated['end_date'],

                'days_count' => $daysCount,

                'nights_count' => $nightsCount,

                'mission_type' => 'local',

                'mission_type_is_archived' => 1,

                'document_id' => $validated['cboDocument'],

                'fileName' => $fileName,

                'program_id' =>
                $validated['cboProgram'] ?? null,

                'program_sub_id' =>
                $validated['cboProgramSub'] ?? null,

                'cluster_id' =>
                $validated['cboCluster'] ?? null,
            ]);

            // ---------------------------------------------------------
            // Soft delete OLD mission employees
            // ---------------------------------------------------------

            MissionEmployee::where(
                'mission_id',
                $mission->id
            )->delete();

            // ---------------------------------------------------------
            // Create CURRENT mission employees
            // ---------------------------------------------------------

            foreach ($employeeIds as $index => $employeeId) {

                $positionId =
                    $validated['cboPosition'][$index] ?? null;

                $assignBudget = (int) (
                    $validated['assign_budget'][$index] ?? 0
                );

                // Employee's own account
                $accountNumber =
                    $validated['account_number'][$index] ?? null;

                // Employee who should receive the payment
                $paymentAccountEmployeeId =
                    $validated['payment_account_employee_id'][$index] ?? null;
                /*
                |--------------------------------------------------------------------------
                | Payment employee
                |--------------------------------------------------------------------------
                */
                $paymentEmployee = Employee::findOrFail(
                    $paymentAccountEmployeeId
                );

                // Get their account number
                $paymentAccountNumber = $paymentEmployee->account_number;

                if (!$positionId) {
                    throw new \Exception(
                        "Position is missing for employee row {$index}."
                    );
                }

                // Position ID
                $position = Positions::findOrFail($positionId);

                // Position -> Level
                $level = Levels::findOrFail($position->level_id);

                // -----------------------------------------------------
                // Money calculation
                // -----------------------------------------------------

                $pocketMoney =
                    $level->pocket_money ?? 0;

                $totalPocketMoney =
                    $pocketMoney * $daysCount;

                $mealMoney =
                    $level->meal_money ?? 0;

                $totalMealMoney =
                    $mealMoney * $daysCount;

                $accommodationMoney =
                    $level->accommodation_money ?? 0;

                $totalAccommodationMoney =
                    $accommodationMoney * $nightsCount;

                // -----------------------------------------------------
                // Travel allowance
                // -----------------------------------------------------

                if ($assignBudget === 1) {

                    $travelAllowance =
                        $province->budget ?? 0;
                } else {

                    $travelAllowance = 0;
                }

                // -----------------------------------------------------
                // Total
                // -----------------------------------------------------

                $total =
                    $travelAllowance
                    + $totalPocketMoney
                    + $totalMealMoney
                    + $totalAccommodationMoney;

                // -----------------------------------------------------
                // Create
                // -----------------------------------------------------

                MissionEmployee::create([

                    'ministry_id' =>
                    $ministry->id,

                    'mission_id' =>
                    $mission->id,

                    'employee_id' =>
                    $employeeId,

                    'position_id' =>
                    $position->id,

                    'level_name' =>
                    $level->name,

                    'travel_allowance' =>
                    $travelAllowance,

                    'pocket_money' =>
                    $pocketMoney,

                    'total_pocket_money' =>
                    $totalPocketMoney,

                    'meal_money' =>
                    $mealMoney,

                    'total_meal_money' =>
                    $totalMealMoney,

                    'accommodation_money' =>
                    $accommodationMoney,

                    'total_accommodation_money' =>
                    $totalAccommodationMoney,

                    'total' =>
                    $total,

                    'assign_budget' =>
                    $assignBudget,

                    'account_number' => $accountNumber,
                    'payment_account_employee_id' => $paymentAccountEmployeeId,
                    'payment_account_number' => $paymentAccountNumber,
                ]);
            }

            // ---------------------------------------------------------
            // COMMIT MUST BE BEFORE REDIRECT
            // ---------------------------------------------------------

            DB::commit();

            // ---------------------------------------------------------
            // Redirect
            // ---------------------------------------------------------

            if ($request->input('submit') === 'save_create') {

                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->success(
                        'បញ្ចូលទិន្នន័យបានជោគជ័យ!',
                        'ជោគជ័យ'
                    )
                    ->flash();
                return redirect()
                    ->route(
                        'missions.create',
                        $params
                    );
            }
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success(
                    'បញ្ចូលទិន្នន័យបានជោគជ័យ!',
                    'ជោគជ័យ'
                )
                ->flash();
            return redirect()
                ->route(
                    'missions.index',
                    $params
                );
        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error(
                'Mission update failed',
                [
                    'mission_id' => $id,
                    'params' => $params,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error(
                    'បញ្ហាក្នុងការកែប្រែ: ' .
                        $e->getMessage(),
                    'បញ្ហា'
                )
                ->flash();

            return redirect()
                ->back()
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        try {

            $ministry = Ministry::where('id', decode_params($params))->first();
            $mission = Mission::where('id', decode_params($id))
                ->where('ministry_id', $ministry->id)
                ->where('payment_is_archived', 1)
                ->firstOrFail();

            if (!$mission) {
                flash()->translate('en')->option('timeout', 2000)
                    ->warning('ទិន្ន័យបានបញ្ចប់', 'Task')->flash();
                return back()->withInput();
            }
            // Cannot delete a paid mission
            if (
                $mission->payment_status === 'paid' &&
                (int) $mission->payment_is_archived === 2
            ) {

                flash()
                    ->translate('en')
                    ->option('timeout', 3000)
                    ->error(
                        'មិនអាចលុបបេសកកម្មនេះបានទេ ព្រោះការទូទាត់បានបង់រួចហើយ។',
                        'មិនអាចលុបបាន'
                    )
                    ->flash();

                return redirect()
                    ->route('missions.index', $params);
            }

            // Delete mission employees first
            MissionEmployee::where(
                'mission_id',
                $mission->id
            )->delete();

            // Soft delete mission
            $mission->delete();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('លុបទិន្នន័យបានជោគជ័យ!', 'ជោគជ័យ')
                ->flash();

            return redirect()
                ->route('missions.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Mission delete failed', [
                'mission_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការលុបទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();


            return redirect()
                ->back();
        }
    }

    public function paymentTotal(Request $request, $params)
    {
        $validated = $request->validate([
            'cboId' => ['sometimes', 'array'],
            'cboId.*' => [
                'required',
                'integer',
                'exists:missions,id',
            ],
            'start_id' => ['nullable', 'integer', 'min:1'],
            'end_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $ministryId = decode_params($params);

        $startId = $validated['start_id'] ?? null;
        $endId = $validated['end_id'] ?? null;
        $selectedIds = $validated['cboId'] ?? [];

        // Validate ID range
        if (
            $startId !== null &&
            $endId !== null &&
            $startId > $endId
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Start ID must be less than or equal to End ID.',
            ], 422);
        }

        $query = DB::table('mission_employees')
            ->join(
                'missions',
                'mission_employees.mission_id',
                '=',
                'missions.id'
            )
            ->where('missions.ministry_id', $ministryId)
            ->whereNull('missions.deleted_at');

        // If an ID range is supplied, calculate all missions in that range.
        if ($startId !== null || $endId !== null) {
            if ($startId !== null) {
                $query->where('missions.id', '>=', $startId);
            }

            if ($endId !== null) {
                $query->where('missions.id', '<=', $endId);
            }
        } else {
            // Otherwise, calculate only selected missions.
            if (empty($selectedIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select missions or enter an ID range.',
                ], 422);
            }

            $query->whereIn(
                'mission_employees.mission_id',
                $selectedIds
            );
        }

        // Get the mission IDs included in the calculation.
        $missionIds = (clone $query)
            ->distinct()
            ->pluck('mission_employees.mission_id')
            ->values()
            ->all();

        $totalAmount = $query->sum('mission_employees.total');

        return response()->json([
            'success' => true,
            'total_amount' => $totalAmount,
            'mission_ids' => $missionIds,
        ]);
    }

    public function updatePaymentStatus(Request $request, $params)
    {
        $validated = $request->validate([
            'cboId' => [
                'required',
                'array',
                'min:1',
            ],

            'cboId.*' => [
                'required',
                'integer',
                'exists:missions,id',
            ],
        ]);

        $ministryId = decode_params($params);

        DB::beginTransaction();

        try {

            $updatedCount = Mission::whereIn(
                'id',
                $validated['cboId']
            )
                ->where('ministry_id', $ministryId)
                ->whereNull('deleted_at')
                ->where(function ($query) {
                    $query->where('payment_status', '!=', 'paid')
                        ->orWhereNull('payment_status');
                })
                ->where('payment_is_archived', '!=', 3)
                ->update([
                    'payment_status' => 'paid',
                    'payment_is_archived' => 3,
                    'paid_at' => now(),
                    'updated_at' => now(),
                ]);

            /*
        |--------------------------------------------------------------------------
        | Nothing updated
        |--------------------------------------------------------------------------
        */

            if ($updatedCount === 0) {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'បេសកកម្មដែលបានជ្រើសបានទូទាត់រួចហើយ។',
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ប្រតិបត្តិការទូទាត់ជោគជ័យ',
                'updated_count' => $updatedCount,

                // IMPORTANT:
                // Return exactly the selected IDs
                'mission_ids' => $validated['cboId'],
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Update Payment Status Error', [
                'message' => $e->getMessage(),
                'mission_ids' => $validated['cboId'] ?? [],
                'ministry_id' => $ministryId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការបង់ប្រាក់។',
            ], 500);
        }
    }

    public function calculatePaymentDraftTotal(Request $request, $params)
    {
        $validated = $request->validate([
            'cboId' => ['required', 'array', 'min:1'],
            'cboId.*' => [
                'required',
                'integer',
                'distinct',
                'exists:missions,id',
            ],
        ]);

        $ministryId = decode_params($params);

        $selectedIds = array_map('intval', $validated['cboId']);

        // Existing missions already included in payment.
        $existingTotal = DB::table('mission_employees')
            ->join(
                'missions',
                'mission_employees.mission_id',
                '=',
                'missions.id'
            )
            ->where('missions.ministry_id', $ministryId)
            ->whereNull('missions.deleted_at')
            ->where('missions.payment_is_archived', 2)
            ->sum('mission_employees.total');

        // Calculate newly selected missions.
        // Exclude missions already included in the existing total.
        $newTotal = DB::table('mission_employees')
            ->join(
                'missions',
                'mission_employees.mission_id',
                '=',
                'missions.id'
            )
            ->where('missions.ministry_id', $ministryId)
            ->whereNull('missions.deleted_at')
            ->whereIn('missions.id', $selectedIds)
            ->where('missions.payment_is_archived', '!=', 2)
            ->sum('mission_employees.total');

        // Combined amount.
        $totalAmount = $existingTotal + $newTotal;

        return response()->json([
            'success' => true,
            'existing_total' => (float) $existingTotal,
            'new_total' => (float) $newTotal,
            'total_amount' => (float) $totalAmount,
            'mission_ids' => array_values($selectedIds),
        ]);
    }

    public function destroyEmployee($params, $id)
    {
        try {
            $missionEmployee = MissionEmployee::find($id);

            if (!$missionEmployee) {
                return response()->json([
                    'success' => false,
                    'message' => 'រកមិនឃើញទិន្នន័យបុគ្គលិក។'
                ], 404);
            }

            $missionEmployee->delete();

            return response()->json([
                'success' => true,
                'message' => flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('delete_msg', 'delete')
                    ->flash(),
            ]);
        } catch (\Exception $e) {

            Log::error('Delete Mission Employee Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការលុបទិន្នន័យ។'
            ], 500);
        }
    }

    public function export(Request $request, $params)
    {
        try {

            // ==========================================================
            // 1. Ministry
            // ==========================================================

            $ministryId = decode_params($params);

            // ==========================================================
            // 2. Validate selected missions
            // ==========================================================

            $validated = $request->validate([
                'cboId' => ['nullable', 'array'],
                'cboId.*' => [
                    'integer',
                    'exists:missions,id',
                ],
            ]);

            $selectedMissionIds =
                $validated['cboId'] ?? [];

            // Remove duplicate IDs
            $selectedMissionIds =
                array_values(array_unique($selectedMissionIds));

            // ==========================================================
            // 3. Base query
            // ==========================================================

            $query = DB::table('missions')

                ->leftJoin(
                    'mission_employees',
                    'missions.id',
                    '=',
                    'mission_employees.mission_id'
                )

                ->leftJoin(
                    'employees',
                    'mission_employees.employee_id',
                    '=',
                    'employees.id'
                )

                ->leftJoin(
                    'positions',
                    'mission_employees.position_id',
                    '=',
                    'positions.id'
                )

                ->leftJoin(
                    'provinces',
                    'missions.province_id',
                    '=',
                    'provinces.id'
                )

                // Ministry
                ->where(
                    'missions.ministry_id',
                    $ministryId
                )

                // Soft delete
                ->whereNull(
                    'missions.deleted_at'
                )
                ->select([
                    'missions.id',
                    'missions.ministry_id',
                    'missions.legal_number',
                    'missions.legal_date',
                    'missions.description',
                    'missions.province_id',
                    'missions.start_date',
                    'missions.end_date',
                    'missions.days_count',
                    'missions.nights_count',
                    'missions.mission_type',

                    'provinces.name as province_name',

                    'mission_employees.employee_title',
                    'mission_employees.employee_id as mission_employee_id',
                    'mission_employees.mission_id as mission_employee_mission_id',
                    'mission_employees.employee_id',
                    'mission_employees.position_id',
                    'mission_employees.level_name',
                    'mission_employees.travel_allowance',
                    'mission_employees.pocket_money',
                    'mission_employees.total_pocket_money',
                    'mission_employees.meal_money',
                    'mission_employees.total_meal_money',
                    'mission_employees.accommodation_money',
                    'mission_employees.total_accommodation_money',
                    'mission_employees.assign_budget',
                    'mission_employees.total',

                    'employees.name_kh',
                    'employees.name_latin',
                    'employees.account_number',
                    'employees.id_number',
                    'employees.created_at',

                    'positions.name as position_name',
                ]);
            // ==========================================================
            // 4. Selected missions
            //
            // If missions were selected, export ONLY those missions.
            // Do not apply the default unpaid filter.
            // ==========================================================

            if (!empty($selectedMissionIds)) {

                $query->whereIn(
                    'missions.id',
                    $selectedMissionIds
                );
            } else {

                // ======================================================
                // 5. No selected missions
                //
                // Use normal filters.
                // ======================================================

                // ------------------------------------------------------
                // Payment Status
                // ------------------------------------------------------

                $cboTodo = $request->input('cboTodo');

                if ($cboTodo == 2) {

                    // Unpaid
                    $query->where(
                        'missions.payment_is_archived',
                        1
                    );
                } elseif ($cboTodo == 3) {

                    // Paid
                    $query->where(
                        'missions.payment_is_archived',
                        2
                    );
                } else {

                    // Default
                    $query->where(
                        'missions.payment_is_archived',
                        1
                    );
                }

                // ------------------------------------------------------
                // Mission Type
                // ------------------------------------------------------

                $missionType =
                    $request->input(
                        'cboMissionType',
                        2
                    );

                if ($missionType == 2) {

                    // Local
                    $query->where(
                        'missions.mission_type_is_archived',
                        1
                    );
                } elseif ($missionType == 3) {

                    // Abroad
                    $query->where(
                        'missions.mission_type_is_archived',
                        2
                    );
                }

                // ------------------------------------------------------
                // Leader / Employee
                // ------------------------------------------------------

                if ($request->filled('cboName')) {

                    $query->where(
                        'missions.leader_id',
                        $request->input('cboName')
                    );
                }

                // ------------------------------------------------------
                // Province
                // ------------------------------------------------------

                if ($request->filled('cboProvince')) {

                    $query->where(
                        'missions.province_id',
                        $request->input('cboProvince')
                    );
                }

                // ------------------------------------------------------
                // Start Date
                // ------------------------------------------------------

                if ($request->filled('start_date')) {

                    $query->whereDate(
                        'missions.start_date',
                        '>=',
                        $request->input('start_date')
                    );
                }

                // ------------------------------------------------------
                // End Date
                // ------------------------------------------------------

                if ($request->filled('end_date')) {

                    $query->whereDate(
                        'missions.end_date',
                        '<=',
                        $request->input('end_date')
                    );
                }
            }

            // ==========================================================
            // 6. Order
            // ==========================================================

            $query->orderBy(
                'missions.created_at',
                'ASC'
            );

            // ==========================================================
            // 7. Get data
            // ==========================================================

            $rows = $query->get();

            // ==========================================================
            // 8. Group by mission ID
            // ==========================================================

            $data = $rows->groupBy('id');

            // ==========================================================
            // 9. Log
            // ==========================================================

            Log::info('Mission Export', [

                'ministry_id' =>
                $ministryId,

                'selected_mission_ids' =>
                $selectedMissionIds,

                'cboTodo' =>
                $request->input('cboTodo'),

                'mission_type' =>
                $request->input('cboMissionType'),

                'province' =>
                $request->input('cboProvince'),

                'employee' =>
                $request->input('cboName'),

                'start_date' =>
                $request->input('start_date'),

                'end_date' =>
                $request->input('end_date'),

                'row_count' =>
                $rows->count(),

                'mission_count' =>
                $data->count(),
            ]);

            // ==========================================================
            // 10. No data
            // ==========================================================

            if ($data->isEmpty()) {

                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(
                        'មិនមានទិន្នន័យសម្រាប់បង្ហាញទេ!',
                        'បញ្ហា'
                    )
                    ->flash();

                return redirect()
                    ->route(
                        'missions.index',
                        $params
                    );
            }

            // ==========================================================
            // 11. Export Excel
            // ==========================================================

            $export = new MissionExport(
                $data,
                $ministryId
            );

            return $export->export($request);
        } catch (\Throwable $e) {

            Log::error(
                'Mission Export Error: ' .
                    $e->getMessage(),
                [
                    'trace' =>
                    $e->getTraceAsString(),

                    'request' =>
                    $request->all(),

                    'params' =>
                    $params,
                ]
            );

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error(
                    'បញ្ហាក្នុងការនាំចេញទិន្នន័យ: ' .
                        $e->getMessage(),
                    'បញ្ហា'
                )
                ->flash();

            return redirect()
                ->route(
                    'missions.index',
                    $params
                );
        }
    }

    public function getPaidMissionExportIds(Request $request, $params)
    {
        $request->validate([
            'paid_at' => ['required', 'date'],
        ]);

        $missionIds = DB::table('missions')
            ->where('ministry_id', decode_params($params))
            ->where('payment_status', 'paid')
            ->whereNull('deleted_at')
            ->where('paid_at', $request->input('paid_at'))
            ->distinct()
            ->orderBy('id')
            ->pluck('id');

        if ($missionIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញបេសកកម្មដែលបានទូទាត់ទេ។',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'mission_ids' => $missionIds,
        ]);
    }

    public function getPaymentAccount(Request $request)
    {
        $request->validate([
            'payment_account_employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
            ],
        ]);

        $employee = Employee::query()
            ->select([
                'id',
                'name_kh',
                'name_latin',
                'account_number',
            ])
            ->findOrFail(
                $request->payment_account_employee_id
            );

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name_kh' => $employee->name_kh,
                'name_latin' => $employee->name_latin,
                'account_number' => $employee->account_number,
            ],
        ]);
    }
    private function resolvePaymentAccount(MissionEmployee $missionEmployee): array
    {
        $paymentEmployee =
            $missionEmployee->paymentAccountEmployee;

        if (!$paymentEmployee) {

            $paymentEmployee =
                $missionEmployee->employee;
        }

        return [
            'employee_id' => $paymentEmployee?->id,
            'employee_name' => $paymentEmployee?->name_kh,
            'account_number' => $paymentEmployee?->account_number,
        ];
    }
    public function getMissionPaymentAccounts($missionId)
    {
        $missionEmployees = MissionEmployee::with([
            'employee:id,name_kh,name_latin,account_number',
            'paymentAccountEmployee:id,name_kh,name_latin,account_number',
        ])
            ->where('mission_id', $missionId)
            ->get();

        $payments = $missionEmployees->map(function ($missionEmployee) {

            $receiver =
                $missionEmployee->paymentAccountEmployee
                ?? $missionEmployee->employee;

            return [
                'employee_id' =>
                $missionEmployee->employee_id,

                'employee_name' =>
                $missionEmployee->employee?->name_kh,

                'own_account' =>
                $missionEmployee->account_number,

                'payment_account_employee_id' =>
                $receiver?->id,

                'payment_employee_name' =>
                $receiver?->name_kh,

                'payment_account_number' =>
                $receiver?->account_number,
            ];
        });

        return response()->json([
            'success' => true,
            'payments' => $payments,
        ]);
    }

    // public function savePaymentStatusExportHistory(Request $request, $params)
    // {
    //     $validated = $request->validate([
    //         'export_url' => ['required', 'string', 'max:10000'],
    //         'file_name' => ['nullable', 'string', 'max:255'],
    //         'filters' => ['nullable', 'array'],
    //         'mission_ids' => ['nullable', 'array'],
    //         'mission_ids.*' => [
    //             'integer',
    //             'distinct',
    //             'exists:missions,id',
    //         ],
    //         'start_id' => ['nullable', 'integer', 'min:1'],
    //         'end_id' => ['nullable', 'integer', 'min:1'],
    //         'total_amount' => ['nullable', 'numeric'],
    //     ]);

    //     $ministryId = decode_params($params);

    //     $missionIds = $validated['mission_ids'] ?? [];

    //     // Validate the ID range.
    //     if (
    //         isset($validated['start_id'], $validated['end_id']) &&
    //         $validated['start_id'] > $validated['end_id']
    //     ) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Start ID must be less than or equal to End ID.',
    //         ], 422);
    //     }

    //     // Ensure selected missions belong to the current ministry.
    //     if (!empty($missionIds)) {
    //         $validCount = Mission::whereIn('id', $missionIds)
    //             ->where('ministry_id', $ministryId)
    //             ->whereNull('deleted_at')
    //             ->count();

    //         if ($validCount !== count($missionIds)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Some selected missions are invalid.',
    //             ], 422);
    //         }
    //     }

    //     $filters = $validated['filters'] ?? [];

    //     $filters['mission_ids'] = $missionIds;
    //     $filters['start_id'] = $validated['start_id'] ?? null;
    //     $filters['end_id'] = $validated['end_id'] ?? null;
    //     $filters['total_amount'] = $validated['total_amount'] ?? null;
    //     $filters['ministry_id'] = $ministryId;

    //     $logId = DB::table('payment_export_logs')->insertGetId([
    //         'payment_draft_id' => null,
    //         'exported_by' => auth()->id(),
    //         'export_type' => 'payment_status',
    //         'file_name' => $validated['file_name']
    //             ?? 'payment_status_export.xlsx',
    //         'filters' => json_encode(
    //             $filters,
    //             JSON_UNESCAPED_UNICODE
    //         ),
    //         'exported_at' => now(),
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Export history saved successfully.',
    //         'log_id' => $logId,
    //     ]);
    // }


    public function savePaymentDraft(Request $request, $params)
    {
        $validated = $request->validate([
            'cboId' => ['required', 'array', 'min:1'],
            'cboId.*' => [
                'required',
                'integer',
                'distinct',
                'exists:missions,id',
            ],
        ]);

        $missionIds = array_map('intval', $validated['cboId']);

        $result = DB::transaction(function () use ($missionIds) {

            // Find selected missions that are still drafts.
            $missions = Mission::whereIn('id', $missionIds)
                ->where('payment_status', 'unpaid')
                ->lockForUpdate()
                ->get();

            if ($missions->count() !== count($missionIds)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cboId' => 'ទិន្ន័យបានបង្កើតឡើងរួច.',
                ]);
            }

            $totalAmount = 0;

            foreach ($missions as $mission) {

                // Calculate the total employee payment for this mission.
                $missionTotal = MissionEmployee::where(
                    'mission_id',
                    $mission->id
                )->sum('total');

                // Update the existing mission record.
                $mission->update([
                    'total_amount' => $missionTotal,
                    'created_by' => auth()->id(),
                    'payment_status' => 'pending',
                    'payment_is_archived' => 2,
                ]);

                $totalAmount += $missionTotal;
            }

            return [
                'mission_count' => $missions->count(),
                'total_amount' => $totalAmount,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'ការដាក់ស្នើសេចក្តីព្រាងទូទាត់ប្រាក់បានជោគជ័យ។',
            'mission_count' => $result['mission_count'],
            'total_amount' => (float) $result['total_amount'],
        ]);
    }

    // public function savePaymentDraft(Request $request, $params)
    // {
    //     $validated = $request->validate([
    //         'cboId' => ['required', 'array', 'min:1'],
    //         'cboId.*' => [
    //             'required',
    //             'integer',
    //             'distinct',
    //             'exists:missions,id',
    //         ],
    //     ]);

    //     $missionIds = array_map('intval', $validated['cboId']);

    //     $draft = DB::transaction(function () use ($missionIds) {

    //         $totalAmount = MissionEmployee::whereIn('mission_id', $missionIds)
    //             ->sum('total');

    //         return Mission::create([
    //             'mission_ids'  => $missionIds,
    //             'total_amount' => $totalAmount,
    //             'created_by'   => auth()->id(),
    //             'status'       => 'draft',
    //         ]);
    //     });

    //     return response()->json([
    //         'success'       => true,
    //         'message'       => 'Payment draft saved successfully.',
    //         'draft_id'      => $draft->id,
    //         'mission_count' => count($missionIds),
    //         'total_amount'  => (float) $draft->total_amount,
    //     ]);
    // }

    // View Draft Payment Details
    // public function viewPaymentDraft(Request $request, $params, $id)
    // {
    //     $draft = MissionPaymentDraft::where('id', $id)
    //         ->where('created_by', auth()->id())
    //         ->where('status', 'draft')
    //         ->firstOrFail();

    //     $missionIds = $draft->mission_ids ?? [];

    //     // Retrieve the saved mission records.
    //     $missions = Mission::whereIn('id', $missionIds)
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'draft' => [
    //             'id' => $draft->id,
    //             'mission_ids' => $missionIds,
    //             'mission_count' => count($missionIds),
    //             'total_amount' => $draft->total_amount,
    //             'created_at' => $draft->created_at?->format('Y-m-d H:i'),
    //             'missions' => $missions->map(function ($mission) {
    //                 return [
    //                     'id' => $mission->id,
    //                     'legal_number' => $mission->legal_number ?? '',
    //                 ];
    //             })->values(),
    //         ],
    //     ]);
    // }
}

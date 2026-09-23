<?php

namespace Modules\Mission\App\Http\Controllers;

use App\DataTables\Mission\InitialMissionsDataTable;
use App\DataTables\Mission\MissionDataTable;
use App\Http\Controllers\Controller;
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

    public function getIndex(InitialMissionsDataTable $dataTable)
    {
        // return view('maintenance.maintenance');
        return $dataTable->render('mission::missions.initialMissions.index');
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
        return $dataTable->render('mission::missions.index', [
            'params' => $params,
            'ministry' => $ministry,
            'position' => $position,
            'level' => $level,
            'provinces' => $provinces,
            'employees' => $employees
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
        // $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();


        return view('mission::missions.create')
            ->with('ministry', $ministry)
            ->with('positions', $positions)
            ->with('provinces', $provinces)
            ->with('employees', $employees)
            ->with('params', $params)
            ->with('document', $document)
            ->with('program', $program)
            // ->with('accountSub', $accountSub)
        ;
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
            // 'cboSubAccount'       => 'nullable|integer',
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
                // 'account_sub_id'      => $validated['cboSubAccount'],
            ]);

            foreach ($validated['cboName'] as $index => $employeeId) {

                $positionId = $validated['cboPosition'][$index] ?? null;
                $assignBudget = (int) ($validated['assign_budget'][$index] ?? 0);

                $position = Positions::where('id', $positionId)->first();
                // ទាញយក Level តាមរយៈ $levelId របស់ជួរដេកនីមួយៗ
                $level = Levels::where('id', $position->level_id)->first();
                // ការពារករណីរកមិនឃើញ Level
                // if (!$level) {
                //     throw new \Exception("Level ID {$levelId} not found.");
                // }
                $pocketMoney = $level->pocket_money;
                $totalPocketMoney = $pocketMoney * $daysCount;

                $mealMoney = $level->meal_money;
                $totalMealMoney = $mealMoney * $daysCount;

                $accommodationMoney = $level->accommodation_money;
                $totalAccommodationMoney = $accommodationMoney * $nightsCount;

                // Condition assign budget
                if ($assignBudget === 1) {
                    $travelAllowance = $province->budget ?? 0;
                    $total = $travelAllowance + $totalPocketMoney + $totalMealMoney + $totalAccommodationMoney;
                } else {
                    $travelAllowance = 0;
                    $total = $totalPocketMoney + $totalMealMoney + $totalAccommodationMoney; // កែសម្រួលបន្ថែមតាមតម្រូវការគណនាសរុប
                }

                $usedDays = Mission::whereHas('missionEmployees', function ($query) use ($employeeId) {
                    $query->where('employee_id', $employeeId);
                })
                    ->where(function ($query) use ($startDate) {
                        $query->whereYear('start_date', $startDate->year)
                            ->whereMonth('start_date', $startDate->month);
                    })
                    ->sum('days_count');

                $totalDays = $usedDays + $daysCount;

                if ($totalDays > 10) {

                    $employee = Employee::find($employeeId);

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

                MissionEmployee::create([
                    'ministry_id' => $ministry->id,
                    'mission_id' => $mission->id,
                    'employee_id' => $employeeId,
                    'position_id' => $positionId,
                    'level_name' => $level->name,
                    'travel_allowance' => $travelAllowance,
                    'pocket_money' => $pocketMoney,
                    'total_pocket_money' => $totalPocketMoney,
                    'meal_money' => $mealMoney,
                    'total_meal_money' => $totalMealMoney,
                    'accommodation_money' => $accommodationMoney,
                    'total_accommodation_money' => $totalAccommodationMoney,
                    'total' => $total,
                    'assign_budget' => $assignBudget // ប្រើអញ្ញាតដែលបានបំលែងជា int រួច
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

                'mission_employees.id as mission_employee_id',
                'mission_employees.travel_allowance',
                'mission_employees.pocket_money',
                'mission_employees.total_pocket_money',
                'mission_employees.meal_money',
                'mission_employees.total_meal_money',
                'mission_employees.accommodation_money',
                'mission_employees.total_accommodation_money',
                'mission_employees.assign_budget',
                'mission_employees.total',

                'employees.id as employee_id',
                'employees.name_kh',
                'employees.name_latin',
                'employees.account_number',
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
            ->firstOrFail();

        $missionEmployees = MissionEmployee::where('mission_id', $mission->id)
            ->get();

        $positions = Positions::orderBy('name')->get();

        $provinces = Province::orderBy('name')->get();

        // Locate active budget voucher record
        $module = Mission::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->where('payment_is_archived', 2)
            ->first();

        // Return warning flash if voucher record is not found or processed
        if (!$module) {
            flash()->translate('en')->option('timeout', 2000)
                ->warning('ទិន្ន័យបានបញ្ចប់', 'Task')->flash();
            return back()->withInput();
        }

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

        return view('mission::missions.edit')
            ->with('ministry', $ministry)
            ->with('mission', $mission)
            ->with('positions', $positions)
            ->with('provinces', $provinces)
            ->with('employees', $employees)
            ->with('params', $params)
            ->with('document', $document)
            ->with('program', $program)
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

            'submit' => 'required|in:save,save_create',
        ]);

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
            dd(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
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

            // Locate active budget voucher record
            $module = Mission::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->where('payment_is_archived', 2)
                ->first();

            // Return warning flash if voucher record is not found or processed
            if (!$module) {
                flash()->translate('en')->option('timeout', 2000)
                    ->warning('ទិន្ន័យបានបញ្ចប់', 'Task')->flash();
                return back()->withInput();
            }

            $mission = Mission::where('id', decode_params($id))
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();
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

    public function paymentTotal(Request $request)
    {
        $request->validate([
            'cboId' => 'required|array',
            'cboId.*' => 'required',
        ]);

        $totalAmount = MissionEmployee::whereIn('mission_id', $request->cboId)
            ->sum('total');

        return response()->json([
            'success' => true,
            'total_amount' => $totalAmount,
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

        DB::beginTransaction();

        try {

            $updatedCount = Mission::whereIn('id', $validated['cboId'])
                ->where('payment_status', '!=', 'paid')
                ->where('payment_is_archived', '!=', 2)
                ->update([
                    'payment_status' => 'paid',
                    'payment_is_archived' => 2,
                    'updated_at' => now(),
                ]);

            // Nothing was updated
            if ($updatedCount === 0) {

                DB::rollBack();

                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(
                        'completed',
                        'Payment'
                    )
                    ->flash();
                return redirect()->route('missions.index', $params);
            }

            DB::commit();

            // Success flash
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('ប្រតិបត្តិការទូទាត់ជោគជ័យ', 'paid')
                ->flash();

            return redirect()->route('missions.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error(
                    'បញ្ហាក្នុងការបង់ប្រាក់: ' . $e->getMessage(),
                    'បញ្ហា'
                )
                ->flash();

            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការបង់ប្រាក់។',
            ], 500);
        }
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
}

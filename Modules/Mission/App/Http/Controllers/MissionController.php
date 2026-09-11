<?php

namespace Modules\Mission\App\Http\Controllers;

use App\DataTables\Mission\InitialMissionsDataTable;
use App\DataTables\Mission\MissionDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Levels;
use App\Models\Content\Ministry;
use App\Models\Content\Employee;
use App\Models\Content\Positions;
use App\Models\Mission\Mission;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Monolog\Level;

class MissionController extends Controller
{

    public function getIndex(InitialMissionsDataTable $dataTable)
    {
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
        $employee = Employee::all();
        return $dataTable->render('mission::missions.index', [
            'params' => $params,
            'ministry' => $ministry,
            'position' => $position,
            'level' => $level,
            'provinces' => $provinces,
            'employee' => $employee
        ]);
    }

    public function getByLevel(Request $request)
    {
        if ($request->position_id) {
            $data = Levels::select('id', 'position_id', 'name')
                ->where('position_id', $request->position_id)
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
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $position = Positions::all();
        $provinces = Province::all();
        $employee = Employee::all();


        return view('mission::missions.create')
            ->with('ministry', $ministry)
            ->with('position', $position)
            ->with('provinces', $provinces)
            ->with('employee', $employee)
            ->with('params', $params)
        ;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {

    // dd($request->all());
        $validated = $request->validate([
            'legal_number'     => 'required|integer',
            'legal_date'  => 'required|date',

            'cboName'             => 'required|array|min:1',
            'cboName.*'             => 'required|integer|exists:employees,id',

            'cboPosition'             => 'required|array|min:1',
            'cboPosition.*'             => 'required|integer|exists:levels,id',

            'cboLevel'             => 'required|array|min:1',
            'cboLevel.*'             => 'required|integer|exists:levels,id',

            'cboProvince'        => 'required|integer|exists:provinces,id',

            'start_date'  => 'required|date',
            'end_date'  => 'required|date|after_or_equal:start_date',

            'txtDescription' => 'required|string|max:9999',

            // 'mission_type' => 'required|integer',

            // Assign checkbox
            'assign_budget' => 'required|array',
            'assign_budget.*' => 'required|boolean'
        ]);

        dd($validated);
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

            // Example:
            // 01 -> 01 = 1 day, 0 nights
            // 01 -> 02 = 2 days, 1 night

            // Total count day and night in mission
            $daysCount = $startDate->diffInDays($endDate) + 1;
            $nightsCount = max($daysCount - 1, 0);

            $position = Positions::all();
            $level    = Levels::where('id', $position->level_id)->first();
            $province = Province::where('id', $validated['cboProvince'])->first();

            /*
        |--------------------------------------------------------------------------
        | Create Mission for each employee
        |--------------------------------------------------------------------------
        */

            foreach ($validated['cboName'] as $index => $employeeId) {

                $positionId = $validated['cboPosition'][$index] ?? null;
                $levelId    = $validated['cboLevel'][$index] ?? null;

                // 1 = assigned
                // 0 = not assigned
                $assignBudget = (int) ($validated['assign_budget'][$index] ?? 0);

                /*
                |--------------------------------------------------------------------------
                | Budget
                |--------------------------------------------------------------------------
                |
                | Put your budget calculation here.
                |
                | Currently:
                | assigned = use budget calculation
                | not assigned = null
                |
                */



                $pocketMoney = $level->pocket_money;
                $totalPocketMoney = $pocketMoney * $daysCount;

                $mealMoney = $level->meal_money;
                $totalMealMoney = $mealMoney * $daysCount;

                $accommodationMoney = $level->accommodation_money;
                $totalAccommodationMoney = $accommodationMoney * $nightsCount;

                // Condition assign budget
                if ($assignBudget === 1) {

                    $travelAllowance = null;
                    $travelAllowance = $province->budget;
                    $total = $travelAllowance + $totalPocketMoney + $totalMealMoney + $totalAccommodationMoney;
                } else {
                    $travelAllowance = null;
                }

                $is_archived = 1;


                Mission::create([
                    'ministry_id' => $ministry->id,

                    'employee_id' => $employeeId,
                    'position_id' => $positionId,
                    'level_id' => $levelId,

                    'legal_number' => $validated['legal_number'],
                    'legal_date' => $validated['legal_date'],
                    'description' => $validated['txtDescription'],
                    'province_id' => $validated['cboProvince'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],

                    'days_count' => $daysCount,
                    'nights_count' => $nightsCount,

                    'travel_allowance' => $travelAllowance,

                    'pocket_money' => $pocketMoney,
                    'total_pocket_money' => $totalPocketMoney,

                    'meal_money' => $mealMoney,
                    'total_meal_money' => $totalMealMoney,

                    'accommodation_money' => $accommodationMoney,
                    'total_accommodation_money' => $totalAccommodationMoney,

                    'total' => $total,

                    // 'mission_type' => $is_archived == 1 ? 'local' : 'abroad',
                    'mission_type' => $is_archived == 1 ? 'local' : 'abroad',
                    'mission_type_is_archived' => $is_archived,

                    'assign_budget' => $validated['assign_budget']
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
    // public function store(Request $request, $params)
    // {
    //     DB::beginTransaction();

    //     try {

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Validate request
    //     |--------------------------------------------------------------------------
    //     */
    //         $validated = $request->validate([
    //             'legal_number' => [
    //                 'required',
    //                 'integer',
    //                 'min:1',
    //             ],

    //             'legal_date' => [
    //                 'required',
    //                 'date',
    //             ],

    //             'start_date' => [
    //                 'required',
    //                 'date',
    //             ],

    //             'end_date' => [
    //                 'required',
    //                 'date',
    //                 'after_or_equal:start_date',
    //             ],

    //             'cboProvince' => [
    //                 'required',
    //                 'integer',
    //                 'exists:provinces,id',
    //             ],

    //             'txtDescription' => [
    //                 'required',
    //                 'string',
    //                 'max:9999',
    //             ],

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Employees
    //         |--------------------------------------------------------------------------
    //         */
    //             'cboName' => [
    //                 'required',
    //                 'array',
    //                 'min:1',
    //             ],

    //             'cboName.*' => [
    //                 'required',
    //                 'integer',
    //                 'exists:employees,id',
    //             ],

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Positions
    //         |--------------------------------------------------------------------------
    //         */
    //             'cboPosition' => [
    //                 'required',
    //                 'array',
    //                 'min:1',
    //             ],

    //             'cboPosition.*' => [
    //                 'required',
    //                 'integer',
    //                 'exists:positions,id',
    //             ],

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Levels
    //         |--------------------------------------------------------------------------
    //         */
    //             'cboLevel' => [
    //                 'required',
    //                 'array',
    //                 'min:1',
    //             ],

    //             'cboLevel.*' => [
    //                 'required',
    //                 'integer',
    //                 'exists:levels,id',
    //             ],

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Assign Budget
    //         |--------------------------------------------------------------------------
    //         */
    //             'assign_budget' => [
    //                 'required',
    //                 'array',
    //                 'min:1',
    //             ],

    //             'assign_budget.*' => [
    //                 'required',
    //                 'boolean',
    //             ],
    //         ]);
    //         /*
    //     |--------------------------------------------------------------------------
    //     | Decode ministry ID
    //     |--------------------------------------------------------------------------
    //     */
    //         $id = decode_params($params);

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Get Ministry
    //     |--------------------------------------------------------------------------
    //     */
    //         $ministry = Ministry::find($id);

    //         if (!$ministry) {
    //             throw new \Exception('Ministry not found.');
    //         }

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Get Province
    //     |--------------------------------------------------------------------------
    //     */
    //         $province = Province::find($validated['cboProvince']);

    //         if (!$province) {
    //             throw new \Exception('Province not found.');
    //         }

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Calculate days / nights
    //     |--------------------------------------------------------------------------
    //     */
    //         $startDate = Carbon::parse($validated['start_date']);
    //         $endDate   = Carbon::parse($validated['end_date']);

    //         /*
    //     Example:

    //     01 -> 01 = 1 day / 0 nights
    //     01 -> 02 = 2 days / 1 night
    //     01 -> 03 = 3 days / 2 nights
    //     */

    //         $daysCount = $startDate->diffInDays($endDate) + 1;

    //         $nightsCount = max($daysCount - 1, 0);

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Create Mission for each employee
    //     |--------------------------------------------------------------------------
    //     */
    //         foreach ($validated['cboName'] as $index => $employeeId) {

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Get values for current row
    //         |--------------------------------------------------------------------------
    //         */
    //             $positionId = $validated['cboPosition'][$index] ?? null;
    //             $levelId    = $validated['cboLevel'][$index] ?? null;

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Assign budget
    //         |--------------------------------------------------------------------------
    //         */
    //             $assignBudget = (int) ($validated['assign_budget'][$index] ?? 0);

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Get level for current employee
    //         |--------------------------------------------------------------------------
    //         */
    //             $level = Levels::find($levelId);

    //             if (!$level) {
    //                 throw new \Exception(
    //                     "Level not found for employee row " . ($index + 1)
    //                 );
    //             }

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Calculate Pocket Money
    //         |--------------------------------------------------------------------------
    //         */
    //             $pocketMoney = (float) $level->pocket_money;

    //             $totalPocketMoney =
    //                 $pocketMoney * $daysCount;

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Calculate Meal Money
    //         |--------------------------------------------------------------------------
    //         */
    //             $mealMoney = (float) $level->meal_money;

    //             $totalMealMoney =
    //                 $mealMoney * $daysCount;

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Calculate Accommodation Money
    //         |--------------------------------------------------------------------------
    //         */
    //             $accommodationMoney =
    //                 (float) $level->accommodation_money;

    //             $totalAccommodationMoney =
    //                 $accommodationMoney * $nightsCount;

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Travel Allowance
    //         |--------------------------------------------------------------------------
    //         */
    //             $travelAllowance = 0;

    //             if ($assignBudget === 1) {

    //                 $travelAllowance = (float) $province->budget;
    //             }

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Calculate Total
    //         |--------------------------------------------------------------------------
    //         */
    //             $total =
    //                 $travelAllowance
    //                 + $totalPocketMoney
    //                 + $totalMealMoney
    //                 + $totalAccommodationMoney;

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Mission Type
    //         |--------------------------------------------------------------------------
    //         */
    //             $isArchived = 1;

    //             $missionType = 'local';

    //             /*
    //         |--------------------------------------------------------------------------
    //         | Create Mission
    //         |--------------------------------------------------------------------------
    //         */
    //             Mission::create([

    //                 'ministry_id' => $ministry->id,

    //                 'employee_id' => $employeeId,

    //                 'position_id' => $positionId,

    //                 'level_id' => $levelId,

    //                 'legal_number' => $validated['legal_number'],

    //                 'legal_date' => $validated['legal_date'],

    //                 'description' => $validated['txtDescription'],

    //                 'province_id' => $validated['cboProvince'],

    //                 'start_date' => $validated['start_date'],

    //                 'end_date' => $validated['end_date'],

    //                 'days_count' => $daysCount,

    //                 'nights_count' => $nightsCount,

    //                 'travel_allowance' => $travelAllowance,

    //                 'pocket_money' => $pocketMoney,

    //                 'total_pocket_money' => $totalPocketMoney,

    //                 'meal_money' => $mealMoney,

    //                 'total_meal_money' => $totalMealMoney,

    //                 'accommodation_money' => $accommodationMoney,

    //                 'total_accommodation_money' => $totalAccommodationMoney,

    //                 'total' => $total,

    //                 'mission_type' => $missionType,

    //                 'mission_type_is_archived' => $isArchived,

    //                 /*
    //             | IMPORTANT:
    //             | Store only current employee's value,
    //             | not the entire assign_budget array.
    //             */
    //                 'assign_budget' => $assignBudget,
    //             ]);
    //         }

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Commit transaction
    //     |--------------------------------------------------------------------------
    //     */
    //         DB::commit();

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Success message
    //     |--------------------------------------------------------------------------
    //     */
    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->success('success_msg', 'successful')
    //             ->flash();

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Redirect
    //     |--------------------------------------------------------------------------
    //     */
    //         if ($request->has('submit')) {

    //             return redirect()->route(
    //                 'missions.index',
    //                 $params
    //             );
    //         }

    //         return redirect()->route(
    //             'missions.create',
    //             $params
    //         );
    //     } catch (\Illuminate\Validation\ValidationException $e) {

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Validation failed
    //     |--------------------------------------------------------------------------
    //     |
    //     | Do not redirect manually here.
    //     | Laravel will return to the form with validation errors.
    //     |
    //     */
    //         DB::rollBack();

    //         throw $e;
    //     } catch (\Throwable $e) {

    //         /*
    //     |--------------------------------------------------------------------------
    //     | Database / processing error
    //     |--------------------------------------------------------------------------
    //     */
    //         DB::rollBack();

    //         Log::error('Mission store failed', [
    //             'message' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //         ]);

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 5000)
    //             ->error(
    //                 'បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(),
    //                 'បញ្ហា'
    //             )
    //             ->flash();

    //         return redirect()
    //             ->back()
    //             ->withInput();
    //     }
    // }

    // public function store(Request $request, $params)
    // {
    //     $validated = $request->validate([
    //         'legal_number'     => 'required|integer',
    //         'legal_date'       => 'required|date',

    //         'cboName'          => 'required|array|min:1',
    //         'cboName.*'        => 'required|integer|exists:employees,id',

    //         'cboPosition'      => 'required|array|min:1',
    //         'cboPosition.*'    => 'required|integer|exists:positions,id',

    //         'cboLevel'         => 'required|array|min:1',
    //         'cboLevel.*'       => 'required|integer|exists:levels,id',

    //         'cboProvince'      => 'required|integer|exists:provinces,id',

    //         'start_date'       => 'required|date',
    //         'end_date'         => 'required|date|after_or_equal:start_date',

    //         'txtDescription'   => 'required|string|max:9999',

    //         // Assign checkbox
    //         'assign_budget'    => 'required|array',
    //         'assign_budget.*'  => 'required|boolean'
    //     ]);

    //     $id = decode_params($params);
    //     DB::beginTransaction();

    //     try {
    //         $ministry = Ministry::where('id', $id)->first();

    //         $startDate = Carbon::parse($validated['start_date']);
    //         $endDate = Carbon::parse($validated['end_date']);

    //         $daysCount = $startDate->diffInDays($endDate) + 1;
    //         $nightsCount = max($daysCount - 1, 0);

    //         $province = Province::where('id', $validated['cboProvince'])->first();

    //         foreach ($validated['cboName'] as $index => $employeeId) {
    //             $positionId = $validated['cboPosition'][$index] ?? null;
    //             $levelId    = $validated['cboLevel'][$index] ?? null;
    //             $assignBudget = (int) ($validated['assign_budget'][$index] ?? 0);

    //             $level = Levels::where('id', $levelId)->first();

    //             $pocketMoney = $level ? $level->pocket_money : 0;
    //             $totalPocketMoney = $pocketMoney * $daysCount;

    //             $mealMoney = $level ? $level->meal_money : 0;
    //             $totalMealMoney = $mealMoney * $daysCount;

    //             $accommodationMoney = $level ? $level->accommodation_money : 0;
    //             $totalAccommodationMoney = $accommodationMoney * $nightsCount;

    //             $travelAllowance = null;
    //             $total = $totalPocketMoney + $totalMealMoney + $totalAccommodationMoney;

    //             if ($assignBudget === 1 && $province) {
    //                 $travelAllowance = $province->budget;
    //                 $total += $travelAllowance;
    //             }

    //             $is_archived = 1;

    //             Mission::create([
    //                 'ministry_id'               => $ministry->id ?? null,
    //                 'employee_id'               => $employeeId,
    //                 'position_id'               => $positionId,
    //                 'level_id'                  => $levelId,
    //                 'legal_number'              => $validated['legal_number'],
    //                 'legal_date'                => $validated['legal_date'],
    //                 'description'               => $validated['txtDescription'],
    //                 'province_id'               => $validated['cboProvince'],
    //                 'start_date'                => $validated['start_date'],
    //                 'end_date'                  => $validated['end_date'],
    //                 'days_count'                => $daysCount,
    //                 'nights_count'              => $nightsCount,
    //                 'travel_allowance'          => $travelAllowance,
    //                 'pocket_money'              => $pocketMoney,
    //                 'total_pocket_money'        => $totalPocketMoney,
    //                 'meal_money'                => $mealMoney,
    //                 'total_meal_money'          => $totalMealMoney,
    //                 'accommodation_money'       => $accommodationMoney,
    //                 'total_accommodation_money' => $totalAccommodationMoney,
    //                 'total'                     => $total,
    //                 'mission_type'              => $is_archived == 1 ? 'local' : 'abroad',
    //                 'mission_type_is_archived'  => $is_archived,
    //                 'assign_budget'             => $assignBudget
    //             ]);
    //         }

    //         DB::commit();

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->success('success_msg', 'successful')
    //             ->flash();

    //         if ($request->has('submit')) {
    //             return redirect()->route('missions.index', $params);
    //         }

    //         return redirect()->route('missions.create', $params);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error($e->getMessage());

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
    //             ->flash();

    //         return redirect()->route('missions.index', $params);
    //     }
    // }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('mission::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('mission::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}

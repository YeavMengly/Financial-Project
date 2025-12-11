<?php

namespace Modules\Water\App\Http\Controllers;

use App\DataTables\Water\InitialWaterDataTable;
use App\DataTables\Water\WaterDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Ministry;
use App\Models\Province;
use App\Exports\Water\WaterExport;
use App\Models\Water\Water;
use App\Models\Water\WaterEntity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaterController extends Controller
{

    public function getIndex(InitialWaterDataTable $dataTable)
    {
        return $dataTable->render('water::water.initialWater.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(WaterDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $waterEntity = WaterEntity::where('ministry_id', $ministry->id)->get();
        $water = Water::where('ministry_id', $ministry->id)->get();

        return  $dataTable->render('water::water.index', [
            'params' => $params,
            'water' => $water,
            'ministry' => $ministry,
            'waterEntity' => $waterEntity
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();
        $waterEntity = WaterEntity::where('ministry_id', $ministry->id)->get();

        return view('water::water.create')
            ->with('params', $params)
            ->with('waterEntity', $waterEntity);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        // 1) Validate request
        $validated = $request->validate([
            'title_entity'            => 'required|exists:water_entities,id',
            'location_number_use'  => 'required|string|max:255',
            'invoice'               => 'required|string|max:255',
            'date'                  => 'required|date',
            'use_start'             => 'required|date',
            'use_end'               => 'required|date|after_or_equal:use_start',
            'kilo'                  => 'required|numeric',
            'cost_total'            => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {

            $ministry = Ministry::where('id', decode_params($params))->first();
            $waterEntity = WaterEntity::where('id', $validated['title_entity'])
                ->where('ministry_id', $ministry->id)
                ->first();
            Water::create([
                'ministry_id'          => $ministry->id,
                'title_entity'         => $waterEntity->title_entity,
                'location_number_use' => $validated['location_number_use'],
                'invoice'              => $validated['invoice'],
                'date'                 => $validated['date'],
                'use_start'            => $validated['use_start'],
                'use_end'              => $validated['use_end'],
                'kilo'                 => $validated['kilo'],
                'cost_total'           => $validated['cost_total'],
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->has('submit')) {
                return redirect()->route('water.index', $params);
            }

            // Save & create new
            return redirect()->route('water.create', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('water.index', $params);
        }
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('water::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $id = decode_params($id);

        $province = Province::all();
        $ministry = Ministry::where('id', decode_params($params))->first();
        $waterEntity = WaterEntity::where('ministry_id', $ministry->id)->get();
        $module = Water::where('id', $id)
            ->where('ministry_id',  $ministry->id)->first();

        return view('water::water.edit')
            ->with('params', $params)
            ->with('ministry', $ministry)
            ->with('module', $module)
            ->with('waterEntity', $waterEntity)
            ->with('provinces', $province);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        // Same validation rules as store()
        $validated = $request->validate([
            'title_entity'        => 'required|exists:water_entities,id',
            'location_number_use' => 'required|string|max:255',
            'invoice'             => 'required|string|max:255',
            'date'                => 'required|date',
            'use_start'           => 'required|date',
            'use_end'             => 'required|date|after_or_equal:use_start',
            'kilo'                => 'required|numeric',
            'cost_total'          => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', decode_params($params))->first();

            $waterEntity = WaterEntity::where('id', $validated['title_entity'])
                ->where('ministry_id', $ministry->id)
                ->first();

            $water = Water::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->first();

            $water->update([
                'ministry_id'         => $ministry->id,
                'title_entity'        => $waterEntity->title_entity,
                'location_number_use' => $validated['location_number_use'],
                'invoice'             => $validated['invoice'],
                'date'                => $validated['date'],
                'use_start'           => $validated['use_start'],
                'use_end'             => $validated['use_end'],
                'kilo'                => $validated['kilo'],
                'cost_total'          => $validated['cost_total'],
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('water.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការកែប្រែ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('water.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {

        $id = decode_params($id);
        $water = Water::where('id', $id)->first();
        $water->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('water.index', $params);
    }

    // public function restore($params, $id)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $ministryId = decode_params($params);
    //         $recordId   = decode_params($id);

    //         $ministry = Ministry::where('id', $ministryId)->firstOrFail();

    //         $water = Water::withTrashed()
    //             ->where('id', $recordId)
    //             ->where('ministry_id', $ministry->id)
    //             ->firstOrFail();

    //         $water->restore();

    //         DB::commit();

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->success('success_msg', 'successful')
    //             ->flash();

    //         return redirect()->route('water.index', $params);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error($e->getMessage());

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->error('បញ្ហាក្នុងការស្ដារឡើងវិញ: ' . $e->getMessage(), 'បញ្ហា')
    //             ->flash();

    //         return redirect()->route('water.index', $params);
    //     }
    // }

    public function export(Request $request, $params)
    {
        try {
            $ministryId = decode_params($params);

            // $loan = BudgetVoucherLoan::where('ministry_id', $ministryId)->get();

            // // Base query: full BeginVoucher models
            // $query = BeginVoucher::where('account_sub_id', $loan->account_sub_id)
            //     ->where('ministry_id', $ministryId);
            $query = Water::query()
                // ->leftJoin('budget_voucher_loans', 'begin_vouchers.account_sub_id', '=', 'budget_voucher_loans.account_sub_id')
                ->where('waters.ministry_id', $ministryId)
                ->select(
                    'waters.*',
                    // 'budget_voucher_loans.internal_increase',
                    // 'budget_voucher_loans.unexpected_increase',
                    // 'budget_voucher_loans.additional_increase',
                    // 'budget_voucher_loans.total_increase',
                    // 'budget_voucher_loans.decrease',
                    // 'budget_voucher_loans.editorial'
                );

            // Apply filters...
            $data = $query->get();



            // Apply the same filters as in DataTable::query()
            if ($request->filled('agency')) {
                $query->where('agency_id', $request->agency);
            }

            if ($request->filled('account')) {
                $query->where('account_id', $request->account);
            }

            if ($request->filled('accountSub')) {
                $query->where('account_sub_id', $request->accountSub);
            }

            if ($request->filled('no')) {
                $query->where('no', 'like', "%{$request->no}%");
            }

            if ($request->filled('txtDescription')) {
                $query->where('txtDescription', 'like', "%{$request->txtDescription}%");
            }

            $query->orderBy('created_at', 'DESC');

            $data = $query->get();

            Log::info('Exported WaterExport Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            if ($data->isEmpty()) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្នន័យសម្រាប់នាំចេញទេ!', 'បញ្ហា')
                    ->flash();

                return redirect()->route('water.index', $params);
            }

            // Pass filtered data + ministry id into export
            $export = new WaterExport($data, $ministryId);

            // you can pass $request if you want to use date filters/text in header
            return $export->export($request);
        } catch (\Throwable $e) {
            Log::error('Export Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការនាំចេញទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('water.index', $params);
        }
    }
}

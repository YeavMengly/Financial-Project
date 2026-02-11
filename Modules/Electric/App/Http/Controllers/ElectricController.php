<?php

namespace Modules\Electric\App\Http\Controllers;

use App\DataTables\Electric\ElectricDataTable;
use App\DataTables\Electric\InitialElectricDataTable;
use App\Exports\Electric\ElectricExport;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Ministry;
use App\Models\Electric\Electric;
use App\Models\Electric\ElectricEntity;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectricController extends Controller
{

    public function getIndex(InitialElectricDataTable $dataTable)
    {
        return $dataTable->render('electric::electric.initialElectric.index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(ElectricDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $electricEntity = ElectricEntity::where('ministry_id', $ministry->id)->get();
        $electric = Electric::where('ministry_id', $ministry->id)->get();

        return  $dataTable->render('electric::electric.index', [
            'params' => $params,
            'electric' => $electric,
            'ministry' => $ministry,
            'electricEntity' => $electricEntity
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();
        $electricEntity = ElectricEntity::where('ministry_id', $ministry->id)->get();

        return view('electric::electric.create')
            ->with('params', $params)
            ->with('electricEntity', $electricEntity);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        // 1) Validate request
        $validated = $request->validate([
            'title_entity'          => 'required|exists:electric_entities,id',
            'location_number_use'   => 'required|string|max:255',
            'date'                  => 'required|date',
            'use_start'             => 'required|date',
            'use_end'               => 'required|date|after_or_equal:use_start',
            'kilo'                  => 'required|numeric',
            'reactive_energy'       => 'nullable|numeric',
            'cost_total'            => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {

            $ministry = Ministry::where('id', decode_params($params))->first();
            $electricEntity = ElectricEntity::where('id', $validated['title_entity'])
                ->where('ministry_id', $ministry->id)
                ->first();

            Electric::create([
                'ministry_id'          => $ministry->id,
                'title_entity'         => $electricEntity->title_entity,
                'location_number_use'  => $validated['location_number_use'],
                'date'                 => $validated['date'],
                'use_start'            => $validated['use_start'],
                'use_end'              => $validated['use_end'],
                'kilo'                 => $validated['kilo'],
                'reactive_energy'      => $validated['reactive_energy'] ?? null,
                'cost_total'           => $validated['cost_total'],
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->has('submit')) {
                return redirect()->route('electric.index', $params);
            }

            return redirect()->route('electric.create', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('electric.index', $params);
        }
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('electric::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $id = decode_params($id);

        $province = Province::all();
        $ministry = Ministry::where('id', decode_params($params))->first();
        $electricEntity = ElectricEntity::where('ministry_id', $ministry->id)->get();
        $module = Electric::where('id', $id)
            ->where('ministry_id',  $ministry->id)->first();

        return view('electric::electric.edit')
            ->with('params', $params)
            ->with('ministry', $ministry)
            ->with('module', $module)
            ->with('electricEntity', $electricEntity)
            ->with('provinces', $province);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        // Same validation rules as store()
        $validated = $request->validate([
            'title_entity'          => 'required|exists:electric_entities,id',
            'location_number_use'   => 'required|string|max:255',
            'date'                  => 'required|date',
            'use_start'             => 'required|date',
            'use_end'               => 'required|date|after_or_equal:use_start',
            'kilo'                  => 'required|numeric',
            'reactive_energy'       => 'nullable|numeric',
            'cost_total'            => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', decode_params($params))->first();

            $electricEntity = ElectricEntity::where('id', $validated['title_entity'])
                ->where('ministry_id', $ministry->id)
                ->first();

            $electric = Electric::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->first();

            $electric->update([
                'ministry_id'         => $ministry->id,
                'title_entity'        => $electricEntity->title_entity,
                'location_number_use' => $validated['location_number_use'],
                'date'                => $validated['date'],
                'use_start'           => $validated['use_start'],
                'use_end'             => $validated['use_end'],
                'kilo'                => $validated['kilo'],
                'reactive_energy'      => $validated['reactive_energy'] ?? null,
                'cost_total'          => $validated['cost_total'],
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('electric.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការកែប្រែ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('electric.index', $params);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {

        $id = decode_params($id);
        $electric = Electric::where('id', $id)->first();
        $electric->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('electric.index', $params);
    }

    public function export(Request $request, $params)
    {
        try {
            $ministryId = decode_params($params);

            // $loan = BudgetVoucherLoan::where('ministry_id', $ministryId)->get();

            // // Base query: full BeginVoucher models
            // $query = BeginVoucher::where('account_sub_id', $loan->account_sub_id)
            //     ->where('ministry_id', $ministryId);
            $query = Electric::query()
                // ->leftJoin('budget_voucher_loans', 'begin_vouchers.account_sub_id', '=', 'budget_voucher_loans.account_sub_id')
                ->where('electrics.ministry_id', $ministryId)
                ->select(
                    'electrics.*',
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

            Log::info('Exported ElectricExport Count', [
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
            $export = new ElectricExport($data, $ministryId);

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

            return redirect()->route('electric.index', $params);
        }
    }
}

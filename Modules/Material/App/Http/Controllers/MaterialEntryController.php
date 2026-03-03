<?php

namespace Modules\Material\App\Http\Controllers;

use App\DataTables\Material\InitialMaterialEntryDataTable;
use App\DataTables\Material\MaterialEntryDataTable;
use App\Exports\Material\MaterialEntriesExport;
use App\Http\Controllers\Controller;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use App\Models\Material\MaterialEntry;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;;

class MaterialEntryController extends Controller
{

    public function getIndex(InitialMaterialEntryDataTable $dataTable)
    {
        return $dataTable->render('material::materialEntry.initialMaterialEntry.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MaterialEntryDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $materialEntry = MaterialEntry::where('ministry_id', $ministry->id)->get();

        return $dataTable->render('material::materialEntry.index', [
            'params' => $params,
            'ministry' => $ministry,
            'agency' => $agency,
            'materialEntry' => $materialEntry
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $unitType = UnitType::where('name', '!=', 'លីត្រ')->get();

        return view('material::materialEntry.create', [
            'params' => $params,
            'unitType' => $unitType,
            'ministry' => $ministry,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'company_name'  => 'required|string|max:255',
            'stock_number'  => 'required',
            'stock_name'    => 'required|string|max:255',
            'user_entry'    => 'required|string|max:255',
            'p_code'    => 'required|string|max:255',
            'p_name'    => 'required|string|max:255',
            'p_year'    => 'required|string|max:255',
            'title'    => 'required|string|max:255',
            'unit'          => 'required',
            'quantity'      => 'required',
            'price'         => 'required',
            'note'          => 'nullable|string|max:10000',
            'date_entry'    => 'required|date',
            'source'    => 'required|string|max:255',
            'note'         => 'nullable|string|max:10000',
            'refer'         => 'nullable|string|max:10000',
            'file'          => 'nullable|array',
            'file.*'        => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $id)->first();
            $unitType = UnitType::where('id', $validated['unit'])->first();
            $materialTotal = (int)$validated['quantity'] * (float)$validated['price'];
            $dateEntry = \Carbon\Carbon::parse($validated['date_entry'])->format('Y-m-d');

            $paths = [];
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    if ($file->isValid()) {
                        $stored[] = $file->store('materialEntry', 'public');
                    }
                }
            }

            MaterialEntry::create([
                'ministry_id'  => $ministry->id,
                'company_name' => $validated['company_name'],
                'stock_number' => $validated['stock_number'],
                'stock_name'   => $validated['stock_name'],
                'user_entry'   => $validated['user_entry'],
                'p_code'   => $validated['p_code'],
                'p_name'   => $validated['p_name'],
                'p_year'   => $validated['p_year'],
                'title'   => $validated['title'],
                'unit'         => $unitType->name,
                'quantity'     => $validated['quantity'],
                'price'        => $validated['price'],
                'total_price'   => $materialTotal,
                'source'        => $validated['source'],
                'note'        => strip_tags($validated['note']),
                'refer'        => strip_tags($validated['refer']),
                'date_entry'   => $dateEntry,
                'file'         => json_encode($paths),
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('materialEntry.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        }
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('material::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();
        $unitType = UnitType::where('name', '!=', 'លីត្រ')->get();
        $module = MaterialEntry::where('id', decode_params($id))
            ->where('ministry_id', $ministry->id)
            ->first();

        return view(
            'material::materialEntry.edit',
            [
                'params' => $params,
                'unitType' => $unitType,
                'ministry' => $ministry,
                'module' => $module
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'company_name'  => 'required|string|max:255',
            'stock_number'  => 'required',
            'stock_name'    => 'required|string|max:255',
            'user_entry'    => 'required|string|max:255',
            'p_code'    => 'required|string|max:255',
            'p_name'    => 'required|string|max:255',
            'p_year'    => 'required|string|max:255',
            'title'    => 'required|string|max:255',
            'unit'          => 'required',
            'quantity'      => 'required',
            'price'         => 'required',
            'note'          => 'nullable|string|max:10000',
            'date_entry'    => 'required|date',
            'source'    => 'required|string|max:255',
            'note'         => 'nullable|string|max:10000',
            'refer'         => 'nullable|string|max:10000',
            'file'          => 'nullable|array',
            'file.*'        => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $unitType = UnitType::where('id', $validated['unit'])->first();
            $materialTotal = (int)$validated['quantity'] * (float)$validated['price'];
            $dateEntry = \Carbon\Carbon::parse($validated['date_entry'])->format('Y-m-d');

            $materialEntry = MaterialEntry::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->first();
            $paths = [];
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    if ($file->isValid()) {
                        $stored[] = $file->store('materialEntry', 'public');
                    }
                }
            }

            $materialEntry->update([
                'ministry_id'  => $ministry->id,
                'company_name' => $validated['company_name'],
                'stock_number' => $validated['stock_number'],
                'stock_name'   => $validated['stock_name'],
                'user_entry'   => $validated['user_entry'],
                'p_code'   => $validated['p_code'],
                'p_name'   => $validated['p_name'],
                'p_year'   => $validated['p_year'],
                'title'   => $validated['title'],
                'unit'         => $unitType->name,
                'quantity'     => $validated['quantity'],
                'price'        => $validated['price'],
                'total_price'   => $materialTotal,
                'source'        => $validated['source'],
                'note'        => strip_tags($validated['note']),
                'refer'        => strip_tags($validated['refer']),
                'date_entry'   => $dateEntry,
                'file'         => json_encode($paths),
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('materialEntry.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function export(Request $request, $params)
    {

        try {
            $ministryId = decode_params($params);
            $query = MaterialEntry::query()
                // ->leftJoin('budget_voucher_loans', 'begin_vouchers.account_sub_id', '=', 'budget_voucher_loans.account_sub_id')
                ->where('material_entries.ministry_id', $ministryId)
                ->select(
                    'material_entries.*',
                );

            // Apply filters...
            $data = $query->get();

            $query->orderBy('created_at', 'DESC');

            $data = $query->get();

            Log::info('Exported MaterialExport Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            if ($data->isEmpty()) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្នន័យសម្រាប់នាំចូលទេ!', 'បញ្ហា')
                    ->flash();

                return redirect()->route('materialEntry.index', $params);
            }

            // Pass filtered data + ministry id into export
            $export = new MaterialEntriesExport($data, $ministryId);

            // you can pass $request if you want to use date filters/text in header
            return $export->export($request);
        } catch (\Throwable $e) {
            Log::error('Export Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការនាំចូលទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        }
    }
}

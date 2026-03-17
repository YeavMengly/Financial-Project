<?php

namespace Modules\Duel\App\Http\Controllers;

use App\DataTables\Duel\InitialDuelReleaseDataTable;
use App\DataTables\Duel\DuelReleaseDataTable;
use App\Exports\Duel\DuelReleaseExport;
use App\Http\Controllers\Controller;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use App\Models\Duel\DuelEntry;
use App\Models\Duel\DuelRelease;
use App\Models\DuelType;
use App\Models\UnitType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DuelReleaseController extends Controller
{
    public function getIndex(InitialDuelReleaseDataTable $dataTable)
    {
        return $dataTable->render('duel::duelRelease.initialDuelRelease.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(DuelReleaseDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $duelType = DuelType::all();
        $unitType = UnitType::where('name', 'លីត្រ')->get();
        $duelRelease = DuelRelease::where('id', $id)
            ->where('ministry_id', $ministry->id)->get();

        return $dataTable->render('duel::duelRelease.index', [
            'params' => $params,
            'ministry' => $ministry,
            'duelType' => $duelType,
            'unitType' => $unitType,
            'duelRelease' => $duelRelease
        ]);
    }

    /**
     * AJAX: Fetch program sub-options by program ID.
     */
    public function getByStockID(Request $request, $params)
    {
        if ($request->stock_number) {
            $ministryId = decode_params($params);
            $data = DuelEntry::select(
                'duel_entries.id',
                'duel_entries.stock_number',
                'duel_types.name_km'
            )
                ->leftJoin('duel_types', 'duel_entries.item_name', '=', 'duel_types.id')
                ->where('duel_entries.ministry_id', $ministryId)
                ->get();
            $selectedId = $request->selected_id ?? null;

            foreach ($data as $d) {
                $selected = $selectedId == $d->stock_number ? 'selected' : '';
                echo "<option value='{$d->id}' {$selected}>{$d->name_km}</option>";
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();
        $duelType = DuelType::all();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $unitType = UnitType::where('name', 'លីត្រ')->get();
        $duelEntry = DuelEntry::where('ministry_id', $ministry->id)
            ->pluck('stock_number')
            ->unique()
            ->values();

        return view('duel::duelRelease.create')
            ->with('ministry', $ministry)
            ->with('duelType', $duelType)
            ->with('unitType', $unitType)
            ->with('duelEntry', $duelEntry)
            ->with('agency', $agency)
            ->with('params', $params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $ministryId = decode_params($params);

        $validated = $request->validate([
            'stock_number'      => 'required',
            'item_name'         => 'required',           // DuelEntry ID
            // 'unit'              => 'required',
            'agency'            => 'required|integer',
            'receipt_number'    => 'required|string|max:255',
            'user_request'      => 'required|string|max:255',
            'quantity_request'  => 'required|numeric|min:0',
            'date_release'      => 'required|string',
            'title'             => 'required|string|max:255',
            'refer'             => 'required|string',
            'note'              => 'required|string',
            'file'              => 'nullable|array',
            'file.*'            => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {

            // ✅ Correct file storing
            $paths = [];
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    if ($file->isValid()) {
                        $paths[] = $file->store('duelRelease', 'public');
                    }
                }
            }

            $ministry = Ministry::where('id', $ministryId)->firstOrFail();

            // ✅ DuelEntry selected in the dropdown (item_name is DuelEntry ID)
            $duelEntry = DuelEntry::findOrFail($validated['item_name']);

            // ---------------------- 🔥 CORE LOGIC 🔥 ----------------------
            // 1. Initial stock from DuelEntry (first time)
            $initialQuantity = $duelEntry->quantity ?? 0;

            // 2. Find last release for this (ministry + stock_number + item_name)
            $lastRelease = DuelRelease::where('ministry_id', $ministry->id)
                ->where('stock_number', $validated['stock_number'])
                ->where('item_name', $duelEntry->item_name)   // item_name stored as text
                ->orderBy('id', 'desc')
                ->first();

            // 3. quantity_total = balance BEFORE this release
            //    - if no previous release: use initial stock
            //    - else: use last duel_total
            if ($lastRelease) {
                $quantityTotal = $lastRelease->duel_total;   // e.g. 79,400 for the second row
            } else {
                $quantityTotal = $initialQuantity;           // e.g. 79,600 for the first row
            }

            // 4. duel_total = balance AFTER this release
            $duelTotal = $quantityTotal - $validated['quantity_request'];

            // 5. Prevent negative balance
            if ($duelTotal < 0) {
                throw new \Exception('ឥណទានមិនគ្រប់ចំនួន សូមពិនិត្យម្តងទៀត!');
            }
            // ---------------------- 🔥 END CORE LOGIC 🔥 ----------------------

            // date parsing
            try {
                $dateRelease = Carbon::createFromFormat('d/m/Y', $validated['date_release'])->format('Y-m-d');
            } catch (\Exception $e) {
                $dateRelease = $validated['date_release'];
            }

            // ✅ Create DuelRelease record
            DuelRelease::create([
                'ministry_id'      => $ministry->id,
                'stock_number'     => $validated['stock_number'],   // code or number
                'item_name'        => $duelEntry->item_name,        // store name from DuelEntry
                'unit'             => 2,
                'agency'           => $validated['agency'],
                'receipt_number'   => $validated['receipt_number'],
                'user_request'     => $validated['user_request'],
                'quantity_request' => $validated['quantity_request'],

                // ⭐ now matches your table:
                // first row:  quantity_total = 79,600, duel_total = 79,400
                // second row: quantity_total = 79,400, duel_total = 79,250
                'quantity_total'   => $quantityTotal,
                'duel_total'       => $duelTotal,

                'date_release'     => $dateRelease,
                'title'            => $validated['title'],
                'note'             => strip_tags($validated['note'] ?? ''),
                'refer'            => strip_tags($validated['refer']),
                'file'             => json_encode($paths),
            ]);

            // (Optional) also update DuelEntry stock itself:
            // $duelEntry->update(['quantity' => $duelTotal]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('បញ្ចូលទិន្នន័យបានជោគជ័យ!', 'ជោគជ័យ')
                ->flash();

            return redirect()->route('duelRelease.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('DuelRelease Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុកទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return back()->withInput();
        }
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('duel::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $ministry   = Ministry::where('id',  decode_params($params))->first();
        $duelType = DuelType::all();
        $duelRelease = DuelRelease::where('id', decode_params($id))
            ->where('ministry_id', $ministry->id)
            ->first();
        $unitType   = UnitType::where('name', 'លីត្រ')->get();

        $agency     = Agency::where('ministry_id', $ministry->id)->get();
        $duelEntryStock = DuelEntry::where('ministry_id', $ministry->id)
            ->pluck('stock_number')
            ->unique()
            ->values();

        return view('duel::duelRelease.edit')
            ->with('duelRelease', $duelRelease)
            ->with('params', $params)
            ->with('unitType', $unitType)
            ->with('duelType', $duelType)
            ->with('agency', $agency)
            ->with('duelEntry', $duelEntryStock)
            ->with('ministry', $ministry);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {

        $validated = $request->validate([
            'stock_number' => 'required',
            'item_name'  => 'required',
            'quantity_request' => 'required|numeric',
            'agency' => 'required',
            'receipt_number' => 'required',
            'user_request' => 'required',
            'date_release' => 'required|date',
            'refer' => 'required',
            'note' => 'required',
        ]);
        DB::beginTransaction();
        try {

            $ministry = Ministry::where('id', decode_params($params))->first();
            $duelRelease = DuelRelease::where('id', $id)->where('ministry_id', $ministry->id)->first();


            // Update main DuelRelease record
            $duelRelease->update([
                'stock_number' => $validated['stock_number'],
                'item_name' => $validated['item_name'],
                'quantity_request' => $validated['quantity_request'],
                'user_request' => $validated['user_request'],
                'agency' => $validated['agency'],
                'date_release' => $validated['date_release'],
                'refer' => strip_tags($validated['refer']),
                'note'  => strip_tags($validated['note']),
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('duelRelease.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('duelRelease.index', $params);
        }
    }

    public function destroy($params, $id)
    {
        $id = decode_params($id);

        $ministry = Ministry::where('id', decode_params($params))->first();
        $duelRelease = DuelRelease::where('id', $id)
            ->where('ministry_id', $ministry->id)->first();
        $duelRelease->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('duelRelease.index', $params);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function export(Request $request, $params)
    {
        try {
            $ministryId = decode_params($params);
            $query = DuelRelease::query()
                ->where('duel_releases.ministry_id', $ministryId)
                ->select(
                    'duel_releases.*',
                );
            $data = $query->get();

            $query->orderBy('created_at', 'ASC');

            $data = $query->get();

            Log::info('Exported DuelExport Count', [
                'ministry_id' => $ministryId,
                'count'       => $data->count(),
            ]);

            if ($data->isEmpty()) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្នន័យសម្រាប់នាំចេញទេ!', 'បញ្ហា')
                    ->flash();

                return redirect()->route('duelRelease.index', $params);
            }
            $export = new DuelReleaseExport($data, $ministryId);

            return $export->export($request);
            // return view('maintenance.maintenance');
        } catch (\Throwable $e) {
            Log::error('Export Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការនាំចេញទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('duelRelease.index', $params);
        }
    }
}

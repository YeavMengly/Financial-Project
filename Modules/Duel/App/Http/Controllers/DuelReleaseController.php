<?php

namespace Modules\Duel\App\Http\Controllers;

use App\DataTables\Duel\InitialDuelReleaseDataTable;
use App\DataTables\Duel\DuelReleaseDataTable;
use App\Exports\Duel\DuelReleaseExport;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Agency;
use App\Models\BeginCredit\Ministry;
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

            $data = DuelEntry::select('id', 'stock_number', 'item_name')
                ->where('ministry_id', $ministryId)
                ->get();
            $selectedId = $request->selected_id ?? null;

            foreach ($data as $d) {
                $selected = $selectedId == $d->stock_number ? 'selected' : '';
                echo "<option value='{$d->id}' {$selected}>{$d->item_name}</option>";
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
    // public function store(Request $request, $params)
    // {
    //     $ministryId = decode_params($params);

    //     $validated = $request->validate([
    //         'stock_number'      => 'required',
    //         'item_name'         => 'required',
    //         'unit'         => 'required',
    //         'agency'            => 'required|integer',
    //         'receipt_number'    => 'required|string|max:255',
    //         'user_request'      => 'required|string|max:255',
    //         'quantity_request'  => 'required|numeric|min:0',
    //         'date_release'      => 'required|string',
    //         'title'             => 'required|string|max:255',
    //         'refer'             => 'required|string',
    //         'note'              => 'required|string',
    //         'file'          => 'nullable|array',
    //         'file.*'        => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
    //     ]);

    //     DB::beginTransaction();

    //     try {

    //         $paths = [];
    //         if ($request->hasFile('file')) {
    //             foreach ($request->file('file') as $file) {
    //                 if ($file->isValid()) {
    //                     $stored[] = $file->store('duelRelease', 'public');
    //                 }
    //             }
    //         }
    //         $ministry = Ministry::where('id', $ministryId)->first();

    //         $duelEntry = DuelEntry::find($validated['item_name']);

    //         $duelTotal =( $duelEntry->quantity ?? 0 )- $validated['quantity_request'];

    //         dd($duelTotal);
    //         try {
    //             $dateRelease = Carbon::createFromFormat('d/m/Y', $validated['date_release'])->format('Y-m-d');
    //         } catch (\Exception $e) {
    //             $dateRelease = $validated['date_release'];
    //         }

    //         // ✅ Create DuelRelease record
    //         DuelRelease::create([
    //             'ministry_id'       => $ministry->id,
    //             'stock_number'     => $validated['stock_number'],   // or 'stock_number_id'
    //             'item_name'         => $duelEntry->item_name,      // if this is actually ID, rename column later
    //             'unit'      => $validated['unit'],
    //             'agency'         => $validated['agency'],
    //             'receipt_number'    => $validated['receipt_number'],
    //             'user_request'      => $validated['user_request'],
    //             'quantity_request'  => $validated['quantity_request'],
    //             'quantity_total'  => $duelEntry->quantity ?? '0',
    //             'duel_total' => '0',
    //             'date_release'      => $dateRelease,
    //             'title'             => $validated['title'],
    //             'note'         => strip_tags($validated['note'] ?? ''),
    //             'refer'        => strip_tags($validated['refer']),
    //             'file'         => json_encode($paths),
    //         ]);

    //         DB::commit();

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->success('បញ្ចូលទិន្នន័យបានជោគជ័យ!', 'ជោគជ័យ')
    //             ->flash();

    //         return redirect()->route('duelRelease.index', $params);
    //     } catch (\Throwable $e) {
    //         DB::rollBack();

    //         Log::error('DuelRelease Store Error: ' . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString(),
    //         ]);

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->error('បញ្ហាក្នុងការរក្សាទុកទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
    //             ->flash();

    //         return back()->withInput();
    //     }
    // }

    // public function store(Request $request, $params)
    // {
    //     $ministryId = decode_params($params);

    //     $validated = $request->validate([
    //         'stock_number'      => 'required',
    //         'item_name'         => 'required',           // DuelEntry ID
    //         'unit'              => 'required',
    //         'agency'            => 'required|integer',
    //         'receipt_number'    => 'required|string|max:255',
    //         'user_request'      => 'required|string|max:255',
    //         'quantity_request'  => 'required|numeric|min:0',
    //         'date_release'      => 'required|string',
    //         'title'             => 'required|string|max:255',
    //         'refer'             => 'required|string',
    //         'note'              => 'required|string',
    //         'file'              => 'nullable|array',
    //         'file.*'            => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
    //     ]);

    //     DB::beginTransaction();

    //     try {

    //         // ✅ Correct file storing (use $paths, not $stored)
    //         $paths = [];
    //         if ($request->hasFile('file')) {
    //             foreach ($request->file('file') as $file) {
    //                 if ($file->isValid()) {
    //                     $paths[] = $file->store('duelRelease', 'public');
    //                 }
    //             }
    //         }

    //         $ministry = Ministry::where('id', $ministryId)->firstOrFail();

    //         // ✅ DuelEntry selected in the dropdown (item_name is DuelEntry ID)
    //         $duelEntry = DuelEntry::findOrFail($validated['item_name']);

    //         // ---------------------- 🔥 CORE LOGIC 🔥 ----------------------
    //         // initial stock quantity from DuelEntry
    //         $initialQuantity = $duelEntry->quantity ?? 0;

    //         // total already released BEFORE this record
    //         $releasedBefore = DuelRelease::where('ministry_id', $ministry->id)
    //             ->where('stock_number', $validated['stock_number'])
    //             ->where('item_name', $duelEntry->item_name)   // store item_name as text in DuelRelease
    //             ->sum('quantity_request');

    //         // total released INCLUDING this new request
    //         $quantityTotal = $releasedBefore + $validated['quantity_request'];

    //         // remaining stock after this release
    //         $duelTotal = $initialQuantity - $quantityTotal;

    //         // ❌ if remaining < 0, not enough stock → error
    //         if ($duelTotal < 0) {
    //             throw new \Exception('ឥណទានមិនគ្រប់ចំនួន សូមពិនិត្យម្តងទៀត!'); // “credit not enough”
    //         }
    //         // ---------------------- 🔥 END CORE LOGIC 🔥 ----------------------

    //         // date parsing
    //         try {
    //             $dateRelease = Carbon::createFromFormat('d/m/Y', $validated['date_release'])->format('Y-m-d');
    //         } catch (\Exception $e) {
    //             $dateRelease = $validated['date_release'];
    //         }

    //         // ✅ Create DuelRelease record
    //         DuelRelease::create([
    //             'ministry_id'      => $ministry->id,
    //             'stock_number'     => $validated['stock_number'],   // code or number
    //             'item_name'        => $duelEntry->item_name,        // store name from DuelEntry
    //             'unit'             => $validated['unit'],
    //             'agency'           => $validated['agency'],
    //             'receipt_number'   => $validated['receipt_number'],
    //             'user_request'     => $validated['user_request'],
    //             'quantity_request' => $validated['quantity_request'],

    //             // ✅ store total released and remaining
    //             'quantity_total'   => $duelEntry->quantity ?? '0',
    //             'duel_total'       => $duelTotal,

    //             'date_release'     => $dateRelease,
    //             'title'            => $validated['title'],
    //             'note'             => strip_tags($validated['note'] ?? ''),
    //             'refer'            => strip_tags($validated['refer']),
    //             'file'             => json_encode($paths),
    //         ]);

    //         // (Optional) ✅ update DuelEntry stock itself with remaining
    //         // $duelEntry->update(['quantity' => $duelTotal]);

    //         DB::commit();

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->success('បញ្ចូលទិន្នន័យបានជោគជ័យ!', 'ជោគជ័យ')
    //             ->flash();

    //         return redirect()->route('duelRelease.index', $params);
    //     } catch (\Throwable $e) {
    //         DB::rollBack();

    //         Log::error('DuelRelease Store Error: ' . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString(),
    //         ]);

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->error('បញ្ហាក្នុងការរក្សាទុកទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
    //             ->flash();

    //         return back()->withInput();
    //     }
    // }
    public function store(Request $request, $params)
    {
        $ministryId = decode_params($params);

        $validated = $request->validate([
            'stock_number'      => 'required',
            'item_name'         => 'required',           // DuelEntry ID
            'unit'              => 'required',
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
                'unit'             => $validated['unit'],
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

        $duelRelease = DuelRelease::where('id', decode_params($id))
            ->where('ministry_id', $ministry->id)
            ->first();
        $unitType   = UnitType::where('name', 'លីត្រ')->get();

        $agency     = Agency::where('ministry_id', $ministry->id)->get();

        // dd($agency);
        $duelEntryStock = DuelEntry::where('ministry_id', $ministry->id)
            ->pluck('stock_number')
            ->unique()
            ->values();

        return view('duel::duelRelease.edit')
            ->with('duelRelease', $duelRelease)
            ->with('params', $params)
            ->with('unitType', $unitType)
            ->with('agency', $agency)
            ->with('duelEntry', $duelEntryStock)
            ->with('ministry', $ministry);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit($params, $id)
    // {
    //     $ministryId = decode_params($params);

    //     $ministry   = Ministry::where('id', $ministryId)->firstOrFail();
    //     $duelType   = DuelType::all();
    //     $agency     = Agency::where('ministry_id', $ministry->id)->get();
    //     $unitType   = UnitType::where('name', 'លីត្រ')->get();

    //     // ✅ The record to edit
    //     $duelRelease = DuelRelease::where('id', $id)
    //         ->where('ministry_id', $ministry->id)
    //         ->firstOrFail();

    //     // ✅ Stock numbers for this ministry only
    //     $duelEntryStock = DuelEntry::where('ministry_id', $ministry->id)
    //         ->pluck('stock_number')
    //         ->unique()
    //         ->values();

    //     // ✅ Try to find the related DuelEntry by stock + item_name
    //     $currentDuelEntry = DuelEntry::where('ministry_id', $ministry->id)
    //         ->where('stock_number', $duelRelease->stock_number)
    //         ->where('item_name', $duelRelease->item_name)
    //         ->first();

    //     return view('duel::duelRelease.edit', [
    //         'params'          => $params,
    //         'ministry'        => $ministry,
    //         'duelType'        => $duelType,
    //         'unitType'        => $unitType,
    //         'agency'          => $agency,
    //         'duelRelease'     => $duelRelease,
    //         'duelEntry'       => $duelEntryStock,   // list for stock_number dropdown
    //         'currentDuelEntry'=> $currentDuelEntry, // to preselect in item_name dropdown
    //     ]);
    // }


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

            // return $export->export($request);
            return view('maintenance.maintenance');
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

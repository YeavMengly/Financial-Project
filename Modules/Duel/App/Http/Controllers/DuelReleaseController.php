<?php

namespace Modules\Duel\App\Http\Controllers;

use App\DataTables\Duel\InitialDuelReleaseDataTable;
use App\DataTables\Duel\DuelReleaseDataTable;
use App\Exports\Duel\DuelReleaseExport;
use App\Http\Controllers\Controller;
use App\Models\Content\Agency;
use App\Models\Content\ExecutiveUnit;
use App\Models\Content\Ministry;
use App\Models\Duel\DuelEntry;
use App\Models\Duel\DuelRelease;
use App\Models\DuelType;
use App\Models\Material\Projects;
use App\Models\UnitType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DuelReleaseController extends Controller
{
    public function getIndex(InitialDuelReleaseDataTable $dataTable)
    {
        //  return view('maintenance.maintenance');
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
        $executiveUnits = ExecutiveUnit::where('ministry_id', $ministry->id)->get();

        return $dataTable->render('duel::duelRelease.index', [
            'params' => $params,
            'ministry' => $ministry,
            'duelType' => $duelType,
            'unitType' => $unitType,
            'duelRelease' => $duelRelease,
            'executiveUnits' => $executiveUnits
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
                'duel_entries.project_id',
                'duel_entries.item_name',
                'duel_types.name_km'
            )
                ->leftJoin('duel_types', 'duel_entries.item_name', '=', 'duel_types.id')
                ->where('duel_entries.ministry_id', $ministryId)
                ->get();
            $selectedId = $request->selected_id ?? null;

            foreach ($data as $d) {
                $selected = $selectedId == $d->stock_number ? 'selected' : '';
                echo "<option value='{$d->item_name}' {$selected}>{$d->name_km}</option>";
            }
        }
    }

    public function getByAgencyId(Request $request)
    {
        if ($request->agency_id) {
            $data = ExecutiveUnit::select('id', 'agency_id', 'title')
                ->where('agency_id', $request->agency_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = $selectedId == $d->id ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->title}</option>";
            }
            return response($html);
        }

        return response('');
    }

    public function editByAgencyId(Request $request)
    {
        if ($request->agency_id) {
            $data = ExecutiveUnit::select('id', 'agency_id', 'title')
                ->where('agency_id', $request->agency_id)
                ->get();

            $selectedId = $request->selected_id ?? null;

            $html = '';
            foreach ($data as $d) {
                $selected = $selectedId == $d->id ? 'selected' : '';
                $html .= "<option value='{$d->id}' {$selected}>{$d->title}</option>";
            }
            return response($html);
        }

        return response('');
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

        // 2. Query DuelEntry using whereIn for the array of project IDs
        $duelEntry = DuelEntry::select(
            'duel_entries.*',
            'projects.stock_number',
            'projects.stock_name',
            'projects.title as project_title'
        )
            ->leftJoin('projects', 'duel_entries.project_id', '=', 'projects.id')
            ->where('duel_entries.ministry_id', $ministry->id)
            ->whereNull('projects.deleted_at')
            ->whereNull('duel_entries.deleted_at') // Fix: Added table prefix to prevent ambiguous column error
            // ->orderBy('duel_entries.created_at', 'desc') // Optional: Ensure you get the latest entry per project
            ->get()
            ->unique('project_id')
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
    //         'stock_number'     => 'required',
    //         'item_name'        => 'required',
    //         'agency'           => 'nullable|integer',
    //         'cboExecutive'     => 'nullable|integer',
    //         'receipt_number'   => ['required', 'string', 'digits:4'],
    //         'user_request'     => 'required|string|max:255',
    //         'receiver'         => 'nullable|string|max:255',
    //         'quantity_request' => 'required|numeric|min:0',
    //         'date_release'     => 'required|string',
    //         'title'            => 'nullable|string|max:255',
    //         'refer'            => 'required|string',
    //         'note'             => 'required|string',
    //         'file'             => 'nullable|file|max:51200',
    //     ]);

    //     $paths = [];
    //     DB::beginTransaction();

    //     try {
    //         $filePath = null; // 1. Set default to null

    //         // 2. Only attempt to save the file if one was actually uploaded
    //         if ($request->hasFile('file')) {
    //             $path_store = 'uploads/duel/release/' . date('Y-m-d');
    //             if (! File::exists($path_store)) {
    //                 File::makeDirectory($path_store, 0777, true, true);
    //             }
    //             $filePath = $request->file('file')->store($path_store, 'public');
    //             $paths[] = $filePath;
    //         }

    //         $ministry  = Ministry::where('id', $ministryId)->firstOrFail();

    //         // Fallback just in case item_name gets lost during a validation failure elsewhere
    //         // $duelEntry = DuelEntry::findOrFail($validated['item_name'] ?? $request->item_name);
    //         // ✅ Correct
    //         $duelEntry = DuelEntry::where('project_id', $validated['stock_number'])
    //             ->where('ministry_id', $ministry->id)
    //             ->firstOrFail();
    //         try {
    //             $dateRelease = Carbon::createFromFormat('d/m/Y', $validated['date_release'])->format('Y-m-d');
    //         } catch (\Exception $e) {
    //             $dateRelease = $validated['date_release'];
    //         }

    //         $this->recalculateLedger($ministry->id, $validated['stock_number'], $validated['item_name']);

    //         DuelRelease::create([
    //             'ministry_id'       => $ministry->id,
    //             'project_id'        => $duelEntry->project_id,
    //             'duel_entries_id'   => $duelEntry->id,
    //             // 'stock_number'      => $validated['stock_number'],
    //             'item_name'         => $validated['item_name'],
    //             'receipt_number'    => $validated['receipt_number'],

    //             // 3. Add `?? null` to ANY field that can be skipped/disabled via JavaScript
    //             'agency'            => $validated['agency'] ?? null,
    //             'executive_unit_id' => $validated['cboExecutive'] ?? null,
    //             'title'             => $validated['title'] ?? null,

    //             'user_request'      => $validated['user_request'],
    //             'receiver'          => $validated['receiver'] ?? null,
    //             'unit'              => 2,
    //             'quantity_total'    => 0,
    //             'quantity_request'  => $validated['quantity_request'],
    //             'quantity_remain'        => 0,
    //             'date_release'      => $dateRelease,
    //             'note'              => strip_tags($validated['note']),
    //             'refer'             => strip_tags($validated['refer']),

    //             // 4. This will insert the path string, or NULL if it was skipped
    //             'file'              => $filePath,
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

    //         foreach ($paths as $path) {
    //             if (Storage::disk('public')->exists($path)) {
    //                 Storage::disk('public')->delete($path);
    //             }
    //         }

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
            'stock_number'     => 'required',
            'item_name'        => 'required',
            'agency'           => 'nullable|integer',
            'cboExecutive'     => 'nullable|integer',
            'receipt_number'   => ['required', 'string', 'digits:4'],
            'user_request'     => 'required|string|max:255',
            'receiver'         => 'nullable|string|max:255',
            'quantity_request' => 'required|numeric|min:0.01',
            'date_release'     => 'required|date',
            'title'            => 'nullable|string|max:255',
            'refer'            => 'required|string',
            'note'             => 'required|string',
            'file'             => 'nullable|file|max:51200',
        ]);

        $paths = [];

        DB::beginTransaction();

        try {

            // =========================================================
            // 1. Get Ministry
            // =========================================================

            $ministry = Ministry::where('id', $ministryId)
                ->firstOrFail();

            // =========================================================
            // 2. Get DuelEntry
            // =========================================================

            $duelEntry = DuelEntry::where('project_id', $validated['stock_number'])
                ->where('ministry_id', $ministry->id)
                ->where('item_name', $validated['item_name'])
                ->firstOrFail();

            // =========================================================
            // 3. Check current stock
            // =========================================================

            $stockQuantity = (float) ($duelEntry->quantity ?? 0);

            if ($stockQuantity <= 0) {
                throw new \Exception(
                    'សម្ភារៈនេះមិនមានចំនួននៅសល់ទេ!'
                );
            }

            // =========================================================
            // 4. Calculate existing requested quantity
            // =========================================================

            $alreadyRequested = DuelRelease::where('ministry_id', $ministry->id)
                ->where('duel_entries_id', $duelEntry->id)
                ->sum('quantity_request');

            $alreadyRequested = (float) $alreadyRequested;

            // =========================================================
            // 5. New request quantity
            // =========================================================

            $newRequest = (float) $validated['quantity_request'];

            // =========================================================
            // 6. Calculate total requested
            // =========================================================

            $totalRequested = $alreadyRequested + $newRequest;

            // =========================================================
            // 7. Check quantity
            // =========================================================

            if ($totalRequested > $stockQuantity) {

                $remaining = $stockQuantity - $alreadyRequested;

                throw new \Exception(
                    "ចំនួនមិនគ្រប់! ចំនួនមាន: {$stockQuantity}, " .
                        "បានស្នើរួច: {$alreadyRequested}, " .
                        "អាចស្នើបានតែ: {$remaining}"
                );
            }

            // =========================================================
            // 8. Upload file
            // =========================================================

            $filePath = null;

            if ($request->hasFile('file')) {

                $pathStore = 'uploads/duel/release/' . date('Y-m-d');

                if (!File::exists($pathStore)) {
                    File::makeDirectory(
                        $pathStore,
                        0777,
                        true,
                        true
                    );
                }

                $filePath = $request->file('file')
                    ->store($pathStore, 'public');

                $paths[] = $filePath;
            }

            // =========================================================
            // 9. Convert date
            // =========================================================
            try {
                $dateRelease = Carbon::createFromFormat('d/m/Y', $validated['date_release'])->format('Y-m-d');
            } catch (\Exception $e) {
                $dateRelease = $validated['date_release'];
            }

            // =========================================================
            // 10. Create DuelRelease
            // =========================================================

            DuelRelease::create([
                'ministry_id'       => $ministry->id,
                'project_id'        => $duelEntry->project_id,
                'duel_entries_id'   => $duelEntry->id,

                'item_name'         => $validated['item_name'],
                'receipt_number'    => $validated['receipt_number'],

                'agency'            => $validated['agency'] ?? null,
                'executive_unit_id' => $validated['cboExecutive'] ?? null,
                'title'             => $validated['title'] ?? null,

                'user_request'      => $validated['user_request'],
                'receiver'          => $validated['receiver'] ?? null,

                'unit'              => 2,

                'quantity_total'    => 0,
                'quantity_request'  => $newRequest,
                'quantity_remain'   => 0,

                'date_release'      => $dateRelease,

                'note'              => strip_tags($validated['note']),
                'refer'             => strip_tags($validated['refer']),

                'file'              => $filePath,
            ]);

            // =========================================================
            // 11. Recalculate ALL releases
            // =========================================================

            $this->recalculateLedger(
                $ministry->id,
                $validated['stock_number'],
                $validated['item_name']
            );

            // =========================================================
            // 12. Commit
            // =========================================================

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success(
                    'បញ្ចូលទិន្នន័យបានជោគជ័យ!',
                    'ជោគជ័យ'
                )
                ->flash();

            return redirect()->route(
                'duelRelease.index',
                $params
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            // Delete uploaded file if transaction failed
            foreach ($paths as $path) {

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            Log::error('DuelRelease Store Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 3000)
                ->error(
                    $e->getMessage(),
                    'បញ្ហា'
                )
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

        // 1. Fetch the specific record you are editing
        $duelRelease = DuelRelease::where('id', decode_params($id))
            ->where('ministry_id', $ministry->id)
            ->first();
        $unitType   = UnitType::where('name', 'លីត្រ')->get();
        // 2. Get an array of all project IDs for this ministry
        $projectIds = Projects::where('ministry_id', $ministry->id)->pluck('id');

        // 3. Query DuelEntry to populate the dropdown choices
        // $duelEntry = DuelEntry::where('ministry_id', $ministry->id)
        //     ->whereIn('project_id', $projectIds)
        //     ->select('stock_number', 'stock_name')
        //     ->distinct()
        //     ->get();
        $duelEntry = DuelEntry::select(
            'duel_entries.*',
            'projects.stock_number',
            'projects.stock_name',
            'projects.title as project_title'
        )
            ->where('duel_entries.ministry_id', $ministry->id)
            ->leftJoin('projects', 'duel_entries.project_id', '=', 'projects.id')
            ->whereNull('projects.deleted_at')
            ->get()
            ->unique('project_id')
            ->values();
        // 4. Fetch the agency list for your other dropdown
        $agency = Agency::where('ministry_id', $ministry->id)->get();

        return view('duel::duelRelease.edit')
            ->with('duelRelease', $duelRelease)
            ->with('params', $params)
            ->with('unitType', $unitType)
            ->with('duelType', $duelType)
            ->with('agency', $agency)
            ->with('duelEntry', $duelEntry)
            ->with('ministry', $ministry);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $ministryId = decode_params($params);

        $validated = $request->validate([
            'stock_number'     => 'required',
            'item_name'        => 'required',
            'agency'           => 'nullable|integer',
            'cboExecutive'     => 'nullable|integer',
            'receipt_number'   => ['required', 'string', 'digits:4'],
            'user_request'     => 'required|string|max:255',
            'receiver'         => 'nullable|string|max:255',
            'quantity_request' => 'required|numeric|min:0',
            'date_release'     => 'required|string',
            'title'            => 'nullable|string|max:255',
            'refer'            => 'required|string',
            'note'             => 'required|string',
        ]);

        DB::beginTransaction();
        //    dd($validated);
        try {
            $ministry = Ministry::where('id', $ministryId)->firstOrFail();
            $duelRelease = DuelRelease::where('id', $id)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            // 1. Keep track of old stock details before update in case the item/stock changed
            $oldStockNumber = $duelRelease->project_id;
            $oldItemName = $duelRelease->item_name;

            // 2. Resolve selected DuelEntry (matches store logic)
            $duelEntry = DuelEntry::where('project_id', $validated['stock_number'])
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            // 3. Date parsing (matches store logic)
            try {
                $dateRelease = Carbon::createFromFormat('d/m/Y', $validated['date_release'])->format('Y-m-d');
            } catch (\Exception $e) {
                $dateRelease = $validated['date_release'];
            }

            // 4. File management (append new uploads to existing files)
            // $existingFiles = json_decode($duelRelease->file, true) ?? [];
            // if ($request->hasFile('file')) {
            //     foreach ($request->file('file') as $file) {
            //         if ($file->isValid()) {
            //             $existingFiles[] = $file->store('duelRelease', 'public');
            //         }
            //     }
            // }

            // 5. Update current record basic details
            $duelRelease->update([
                'project_id'        => $duelEntry->project_id,
                'duel_entries_id'   => $duelEntry->id,
                // 'stock_number'      => $validated['stock_number'],
                'item_name'         => $validated['item_name'],
                'agency'           => $validated['agency'] ?? null,
                'executive_unit_id' => $validated['cboExecutive'],
                'receipt_number'   => $validated['receipt_number'],
                'user_request'     => $validated['user_request'],
                'receiver'     => $validated['receiver'],
                'quantity_request' => $validated['quantity_request'],
                'date_release'     => $dateRelease,
                'title'            => $validated['title'] ?? null,
                'refer'            => strip_tags($validated['refer']),
                'note'             => strip_tags($validated['note']),
                // 'file'             => json_encode($existingFiles),
            ]);

            // 6. Recalculate running totals for the new/updated item sequence
            $this->recalculateLedger($ministry->id, $validated['stock_number'], $duelEntry->item_name);

            // 7. If the user changed the item or stock number, recalculate the old stream as well
            if ($oldStockNumber !== $validated['stock_number'] || $oldItemName !== $duelEntry->item_name) {
                $this->recalculateLedger($ministry->id, $oldStockNumber, $oldItemName);
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('ធ្វើបច្ចុប្បន្នភាពទិន្នន័យបានជោគជ័យ!', 'ជោគជ័យ')
                ->flash();

            return redirect()->route('duelRelease.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('DuelRelease Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការធ្វើបច្ចុប្បន្នភាព: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return back()->withInput();
        }
    }

    /**
     * Recalculate running totals (quantity_total & duel_total) for an entire stock item ledger.
     */
    private function recalculateLedger($ministryId, $stockNumber, $itemName)
    {
        // =========================================================
        // 1. Find DuelEntry
        // =========================================================

        $duelEntry = DuelEntry::where('ministry_id', $ministryId)
            ->where('project_id', $stockNumber)
            ->where('item_name', $itemName)
            ->firstOrFail();

        // Initial stock
        $runningBalance = (float) ($duelEntry->quantity ?? 0);

        // =========================================================
        // 2. Get all releases
        // =========================================================

        $releases = DuelRelease::where('ministry_id', $ministryId)
            ->where('duel_entries_id', $duelEntry->id)
            ->where('item_name', $itemName)
            ->orderBy('date_release', 'ASC')
            ->orderBy('receipt_number', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        // =========================================================
        // 3. Recalculate
        // =========================================================

        foreach ($releases as $release) {

            // Stock available before this release
            $quantityTotal = $runningBalance;

            // Requested quantity
            $quantityRequest = (float) $release->quantity_request;

            // Remaining after request
            $duelTotal = $quantityTotal - $quantityRequest;

            // =====================================================
            // Prevent negative balance
            // =====================================================

            if ($duelTotal < 0) {

                throw new \Exception(
                    "ចំនួនមិនគ្រប់! " .
                        "មាន {$quantityTotal} " .
                        "ប៉ុន្តែស្នើ {$quantityRequest}"
                );
            }

            // =====================================================
            // Update ledger
            // =====================================================

            $release->update([
                'quantity_total'  => $quantityTotal,
                'duel_total'      => $duelTotal,
                'quantity_remain' => $duelTotal,
            ]);

            // =====================================================
            // Move balance to next release
            // =====================================================

            $runningBalance = $duelTotal;
        }
    }

    public function destroy($params, $id)
    {
        DB::beginTransaction();

        try {
            $ministryId = decode_params($params);
            $releaseId  = decode_params($id);

            $ministry = Ministry::where('id', $ministryId)->firstOrFail();

            $duelRelease = DuelRelease::where('id', $releaseId)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            // 1. Remember stock details to recalculate ledger after deletion
            $stockNumber = $duelRelease->project_id;
            $itemName    = $duelRelease->item_name;

            // 2. Delete associated file uploads from disk storage
            // if (!empty($duelRelease->file)) {
            //     $files = json_decode($duelRelease->file, true) ?? [];
            //     foreach ($files as $filePath) {
            //         if (Storage::disk('public')->exists($filePath)) {
            //             Storage::disk('public')->delete($filePath);
            //         }
            //     }
            // }
            if ($duelRelease->file) {
                $filePath = $duelRelease->file;

                if (Storage::disk('public')->exists($filePath)) {
                    $trashPath = 'trash/' . $filePath;

                    // Ensure trash directory exists before moving
                    $trashDir = dirname($trashPath);
                    if (!Storage::disk('public')->exists($trashDir)) {
                        Storage::disk('public')->makeDirectory($trashDir);
                    }

                    Storage::disk('public')->move($filePath, $trashPath);

                    $duelRelease->file = $trashPath;
                    $duelRelease->save();
                }
            }

            // 3. Delete the record
            $duelRelease->delete();

            // 4. Recalculate remaining sequence so subsequent rows get updated totals
            $this->recalculateLedger($ministry->id, $stockNumber, $itemName);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('លុបទិន្នន័យបានជោគជ័យ!', 'ជោគជ័យ')
                ->flash();

            return redirect()->route('duelRelease.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('DuelRelease Destroy Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការលុបទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('duelRelease.index', $params);
        }
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
                ->leftJoin('duel_entries', function ($join) use ($ministryId) {
                    $join->on('duel_entries.project_id', '=', 'duel_releases.project_id')
                        ->on('duel_entries.item_name', '=', 'duel_releases.item_name')
                        ->where('duel_entries.ministry_id', '=', $ministryId);
                })
                ->select(
                    'duel_releases.*',
                    'duel_entries.quantity as quantity'
                )
                ->orderBy('duel_releases.date_release', 'ASC')
                ->orderBy('duel_releases.receipt_number', 'ASC');

            if ($request->filled('cboDuelType')) {
                $query->where('duel_releases.item_name', $request->cboDuelType);
            }
            if ($request->filled('cboExecutiveUnit')) {
                $query->where('duel_releases.agency', $request->cboExecutiveUnit);
            }
            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
                $query->whereDate('duel_releases.date_release', '>=', $startDate);
            }

            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
                $query->whereDate('duel_releases.date_release', '<=', $endDate);
            }
            $data = $query->get();
            if ($request->filled('start_date')) {

                $startDate = Carbon::parse($request->start_date)->format('Y-m-d');

                foreach ($data as $item) {

                    $releasedBefore = DuelRelease::where('ministry_id', $ministryId)
                        ->where('stock_number', $item->stock_number)
                        ->where('item_name', $item->item_name)
                        ->whereDate('date_release', '<', $startDate)
                        ->sum('quantity_request');

                    $item->opening_quantity = max(0, $item->quantity - $releasedBefore);
                }
            } else {

                foreach ($data as $item) {
                    $item->opening_quantity = $item->quantity;
                }
            }
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

            $export = new DuelReleaseExport(
                $data,
                $ministryId,
                $request->start_date,
                $request->end_date
            );
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

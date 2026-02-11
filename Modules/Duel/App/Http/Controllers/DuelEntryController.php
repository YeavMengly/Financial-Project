<?php

namespace Modules\Duel\App\Http\Controllers;

use App\DataTables\Duel\DuelEntryDataTable;
use App\DataTables\Duel\InitialDuelEntryDataTable;
use App\Exports\Duel\DuelEntriesExport;
use App\Http\Controllers\Controller;
use App\Models\Content\Ministry;
use App\Models\Duel\DuelEntry;
use App\Models\DuelType;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DuelEntryController extends Controller
{

    public function getIndex(InitialDuelEntryDataTable $dataTable)
    {
        return $dataTable->render('duel::duelEntry.initialDuelEntry.index');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(DuelEntryDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $duelType = DuelType::all();
        $unitType = UnitType::where('name', 'លីត្រ')->get();
        $duelEntry = DuelEntry::where('id', $id)
            ->where('ministry_id', $ministry->id)->get();

        return $dataTable->render('duel::duelEntry.index', [
            'params' => $params,
            'ministry' => $ministry,
            'duelType' => $duelType,
            'unitType' => $unitType,
            'duelEntry' => $duelEntry
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $unitType = UnitType::where('name', 'លីត្រ')->get();
        $duelType = DuelType::all();
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();

        return view('duel::duelEntry.create')
            ->with('params', $params)
            ->with('duelType', $duelType)
            ->with('ministry', $ministry)
            ->with('unitType', $unitType);
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
            'item_name'     => 'required',
            'title'    => 'required|string|max:255',
            'unit'          => 'required',
            'quantity'      => 'required',
            'price'         => 'required',
            'note'          => 'nullable|string|max:10000',
            'date_entry'    => 'required|date',
            'refer'         => 'nullable|string|max:10000',
            'source'        => 'nullable|string|max:255',
            'file'          => 'nullable|array',
            'file.*'        => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $id)->first();
            $duelType = DuelType::where('id', $validated['item_name'])->first();
            $unitType = UnitType::where('id', $validated['unit'])->first();
            $duelTotal = (int)$validated['quantity'] * (float)$validated['price'];
            $dateEntry = \Carbon\Carbon::parse($validated['date_entry'])->format('Y-m-d');

            $paths = [];
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    if ($file->isValid()) {
                        $stored[] = $file->store('duelEntry', 'public');
                    }
                }
            }

            DuelEntry::create([
                'ministry_id'  => $ministry->id,
                'item_name'    => $duelType->name_km,
                'company_name' => $validated['company_name'],
                'stock_number' => $validated['stock_number'],
                'stock_name'   => $validated['stock_name'],
                'user_entry'   => $validated['user_entry'],
                'unit'         => $unitType->name,
                'title'   => $validated['title'],
                'quantity'     => $validated['quantity'],
                'price'        => $validated['price'],
                'duel_total'   => $duelTotal,
                'note'         => strip_tags($validated['note'] ?? ''),
                'refer'        => strip_tags($validated['refer']),
                'date_entry'   => $dateEntry,
                'source'   => $validated['source'],
                'file'         => json_encode($paths),
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('duelEntry.index', $params);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('duelEntry.index', $params);
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

        $id = decode_params($id);
        $ministry = Ministry::where('id', decode_params($params))->first();
        $unitType = UnitType::where('name', 'លីត្រ')->get();
        $duelType = DuelType::all();
        $module = DuelEntry::where('id', $id)
            ->where('ministry_id', $ministry->id)->first();

        return view('duel::duelEntry.edit')
            ->with('params', $params)
            ->with('duelType', $duelType)
            ->with('ministry', $ministry)
            ->with('unitType', $unitType)
            ->with('module', $module);
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
            'item_name'     => 'required',
            'title'    => 'required|string|max:255',
            'unit'          => 'required',
            'quantity'      => 'required',
            'price'         => 'required',
            'note'          => 'nullable|string|max:10000',
            'date_entry'    => 'required|date',
            'refer'         => 'nullable|string|max:10000',
            'source'        => 'nullable|string|max:255',
            'file'          => 'nullable|array',
            'file.*'        => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $duelType = DuelType::where('id', $validated['item_name'])->first();
            $unitType = UnitType::where('id', $validated['unit'])->first();
            $duelTotal = (int)$validated['quantity'] * (float)$validated['price'];
            $dateEntry = \Carbon\Carbon::parse($validated['date_entry'])->format('Y-m-d');

            $paths = [];
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    if ($file->isValid()) {
                        $stored[] = $file->store('duelEntry', 'public');
                    }
                }
            }

            $duelEntry = DuelEntry::where('id', $id)
                ->where('ministry_id', $ministry->id)->first();

            if ($duelEntry) {
                $duelEntry->update([
                    'ministry_id'  => $ministry->id,
                    'item_name'    => $duelType->name_km,
                    'company_name' => $validated['company_name'],
                    'stock_number' => $validated['stock_number'],
                    'stock_name'   => $validated['stock_name'],
                    'user_entry'   => $validated['user_entry'],
                    'unit'         => $unitType->name,
                    'title'   => $validated['title'],
                    'quantity'     => $validated['quantity'],
                    'price'        => $validated['price'],
                    'duel_total'   => $duelTotal,
                    'note'         => strip_tags($validated['note'] ?? ''),
                    'refer'        => strip_tags($validated['refer']),
                    'date_entry'   => $dateEntry,
                    'source'   => $validated['source'],
                    'file'         => json_encode($paths),
                ]);

                DB::commit();

                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->success('success_msg', 'successful')
                    ->flash();
                return redirect()->route('duelEntry.index', $params);
            }
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('duelEntry.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        //
        $id = decode_params($id);
        $ministry = Ministry::where('id', decode_params($params))->first();
        $duelEntry = DuelEntry::where('id', $id)
            ->where('ministry_id', $ministry->id)->first();
        $duelEntry->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('duelEntry.index', $params);
    }

    public function export(Request $request, $params)
    {
        try {
            $ministryId = decode_params($params);
            $query = DuelEntry::query()
                ->where('duel_entries.ministry_id', $ministryId)
                ->select(
                    'duel_entries.*',
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
                    ->error('មិនមានទិន្នន័យសម្រាប់នាំចូលទេ!', 'បញ្ហា')
                    ->flash();

                return redirect()->route('duelEntry.index', $params);
            }
            $export = new DuelEntriesExport($data, $ministryId);

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

            return redirect()->route('duelEntry.index', $params);
        }
    }
}

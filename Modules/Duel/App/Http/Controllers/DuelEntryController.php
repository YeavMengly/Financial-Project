<?php

namespace Modules\Duel\App\Http\Controllers;

use App\DataTables\Duel\DuelEntryDataTable;
use App\DataTables\Duel\InitialDuelEntryDataTable;
use App\Exports\Duel\DuelEntriesExport;
use App\Http\Controllers\Controller;
use App\Models\Content\Ministry;
use App\Models\Duel\DuelEntry;
use App\Models\DuelType;
use App\Models\Material\Projects;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class DuelEntryController extends Controller
{

    public function getIndex(InitialDuelEntryDataTable $dataTable)
    {
        // return view('maintenance.maintenance');
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
        $unitType = UnitType::where('id', 2)->get();

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
        $id   = decode_params($params);

        $duelType = DuelType::all();
        $unitType = UnitType::where('id', 2)->get();
        $ministry = Ministry::where('id', $id)->first();
        $project = Projects::where('ministry_id', $id)
            ->get()
            ->unique('stock_number');
        return view('duel::duelEntry.create')
            ->with('params', $params)
            ->with('duelType', $duelType)
            ->with('ministry', $ministry)
            ->with('unitType', $unitType)
            ->with('projects', $project);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'project'      => 'required|integer',
            'item_name.*' => 'required|string',
            'quantity.*'  => 'required|numeric',
            'price.*'     => 'required|numeric',
            'source'        => 'nullable|string|max:255',
            'pro_year'  => 'nullable|string|max:255',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $id)->first();
            $project = Projects::where('id', $validated['project'])
                ->where('ministry_id', $id)
                ->firstOrFail();

            foreach ($request->item_name as $index => $item) {

                DuelEntry::create([
                    'ministry_id'   => $ministry->id,
                    'project_id'   => $project->id,
                    // 'stock_number'   => $project->stock_number,
                    // 'stock_name'   => $project->stock_name,
                    'item_name'     => $request->item_name[$index],
                    'unit'          => 2,
                    'quantity'      => $request->quantity[$index],
                    'price'         => $request->price[$index],
                    'total_price'    => $request->quantity[$index] * $request->price[$index],
                    'date_entry'    => $project->date,
                    'source'   => $validated['source'] ?? null,
                    'pro_year' => $validated['pro_year'] ?? '',
                ]);
            }

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
        $ministry = Ministry::where('id', decode_params($params))->firstOrFail();

        $unitType = UnitType::where('name', 'លីត្រ')->get();
        $duelType = DuelType::all();
        $projects = Projects::where('ministry_id', $ministry->id)
            ->get()
            ->unique('stock_number');

        $module = DuelEntry::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->firstOrFail();

        return view('duel::duelEntry.edit')
            ->with('params', $params)
            ->with('duelType', $duelType)
            ->with('ministry', $ministry)
            ->with('unitType', $unitType)
            ->with('module', $module)
            ->with('projects', $projects);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'project'     => 'required',
            'item_name'   => 'required|array',
            'item_name.*' => 'required',
            'quantity'    => 'required|array',
            'quantity.*'  => 'required|numeric',
            'price'       => 'required|array',
            'price.*'     => 'required|numeric',
            'source'      => 'nullable|string|max:255',
            'pro_year'    => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->firstOrFail();
            $project  = Projects::where('id', $validated['project'])
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            $dateEntry = !empty($project->date)
                ? \Carbon\Carbon::parse($project->date)->format('Y-m-d')
                : null;

            $items = (array) $request->item_name;

            foreach ($items as $index => $itemName) {
                $qty   = (float) ($request->quantity[$index] ?? 0);
                $price = (float) ($request->price[$index] ?? 0);
                $total = $qty * $price;

                if ($index === 0) {
                    $duelEntry = DuelEntry::where('id', $id)
                        ->where('ministry_id', $ministry->id)
                        ->first();

                    if ($duelEntry) {
                        $duelEntry->update([
                            'ministry_id'  => $ministry->id,
                            'project_id'   => $project->id,
                            // 'stock_number'   => $project->stock_number,
                            // 'stock_name'   => $project->stock_name,

                            'item_name'    => $itemName,
                            'unit'         => $project->unit ?? '',
                            'quantity'     => $qty,
                            'price'        => $price,
                            'total_price'   => $total,
                            'date_entry'   => $dateEntry,
                            'source'       => $validated['source'] ?? '',
                            'pro_year'     => $validated['pro_year'] ?? '',
                        ]);
                    }
                } else {
                    DuelEntry::create([
                        'ministry_id'  => $ministry->id,
                        'project_id'   => $project->id,
                        // 'stock_number'   => $project->stock_number,
                        // 'stock_name'   => $project->stock_name,
                        'item_name'    => $itemName,
                        'unit'         => $project->unit ?? '',
                        'quantity'     => $qty,
                        'price'        => $price,
                        'total_price'   => $total,
                        'date_entry'   => $dateEntry,
                        'source'       => $validated['source'] ?? '',
                        'pro_year'     => $validated['pro_year'] ?? '',
                    ]);
                }
            }

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
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
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

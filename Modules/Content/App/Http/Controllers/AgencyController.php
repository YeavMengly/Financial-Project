<?php

namespace Modules\Content\App\Http\Controllers;

use App\DataTables\Content\AgencyDataTable;
use App\DataTables\AnnualOpen\InitialAgencyDataTable;
use App\DataTables\Content\ExecutiveUnitDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Agency;
use App\Models\Content\AgencyAssignment;
use App\Models\Content\ProgramSub;
use App\Models\Content\Cluster;
use App\Models\Content\ExecutiveUnit;
use App\Models\Content\Ministry;
use App\Models\Content\Program;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgencyController extends Controller
{

    public function getIndex(InitialAgencyDataTable $dataTable)
    {
        return $dataTable->render('content::content.agency.initialAgency.index');
    }

    /**
     * Display a listing of the resource.zz
     */
    public function index(AgencyDataTable $dataTable, $params)
    {
        $id  = decode_params($params);
        $data = Ministry::where('id', $id)->first();

        return $dataTable->render('content::content.agency.index', [
            'data' => $data,
            'params' => $params
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $data = Program::where('ministry_id', $id)->get();

        return view('content::content.agency.create')->with('params', $params)
            ->with('data', $data)->with('ministry', $ministry);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validateData = $request->validate([
            // 'cboProgram' => ['nullable'],
            'no' => ['required'],
            'name' => ['required'],
            'nick_name' => ['required'],
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {

            $ministries = Ministry::where('id', $id)->first();
            Agency::create([
                'ministry_id' => $ministries->id,
                // 'program_id' => $validateData['cboProgram'] ?? null,
                'no' => $validateData['no'],
                'name' => $validateData['name'],
                'nick_name' => $validateData['nick_name'],

            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('agency.index', $params);
        } catch (Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('agency.index', $params);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $id = decode_params($id);
        $ministry = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('ministry_id', decode_params($params))->get();
        $agency = Agency::where('id', $id)->first();

        return view('content::content.agency.edit')->with('params', $params)->with('agency', $agency)->with('program', $program)->with('ministry', $ministry);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validateData = $request->validate([
            // 'cboProgram' => ['nullable'],
            'no' => ['required'],
            'name' => ['required'],
            'nick_name' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $agency = Agency::findOrfail($id);
            $agency->update([
                // 'program_id' => $validateData['cboProgram'] ?? null,
                'no' => $validateData['no'],
                'name' => $validateData['name'],
                'nick_name' => $validateData['nick_name'],
            ]);
            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('agency.index', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('agency.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $id = decode_params($id);
        $agency = Agency::where('id', $id)->first();
        $agency->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('agency.index', $params);
    }

    public function restore($params, $id)
    {
        $pid = decode_params($id);

        Agency::withTrashed()->whereKey($pid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('agency.index', $params);
    }

    public function executiveIndex(ExecutiveUnitDataTable $dataTable, $params, $executiveId)
    {

        $module = Ministry::where('id', decode_params($params))->first();
        $agency = Agency::where('id', decode_params($executiveId))->first();

        return $dataTable->render(
            'content::content.agency.executiveUnit.index',
            [
                'params' => $params,
                'executiveId' => $executiveId,
                'module' => $module,
                'agency' => $agency
            ]
        );
    }

    public function executiveCreate($params, $executiveId)
    {
        $module = Ministry::where('id', decode_params($params))->first();
        $agency = Agency::where('id', decode_params($executiveId))
            ->where('ministry_id', $module->id)->first();

        return view('content::content.agency.executiveUnit.create')->with('params', $params)->with('executiveId', $executiveId)->with('module', $module)->with('agency', $agency);
    }

    public function executiveEdit($params, $executiveId, $id)
    {
        $module = Ministry::where('id', decode_params($params))->first();
        $agency = Agency::where('id', decode_params($executiveId))
            ->where('ministry_id', $module->id)->first();
        $executiveUnit = ExecutiveUnit::where('id', decode_params($id))->first();

        return view('content::content.agency.executiveUnit.edit')->with('params', $params)->with('executiveId', $executiveId)->with('module', $module)->with('agency', $agency)->with('executiveUnit', $executiveUnit);
    }

    public function executiveStore(Request $request, $params, $executiveId)
    {
        $validateData = $request->validate([
            'title' => ['required'],
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {

            $ministries = Ministry::where('id', $id)->first();
            $agency = Agency::where('id', decode_params($executiveId))
                ->where('ministry_id', $ministries->id)->first();

            ExecutiveUnit::create([
                'ministry_id' => $ministries->id,
                'agency_id' => $agency->id,
                'title' => $validateData['title'],
            ]);
        } catch (Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('executiveUnit.index', ['params' => $params, 'executiveId' => $executiveId]);
        }
        DB::commit();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('success_msg', 'successful')
            ->flash();

        return redirect()->route('executiveUnit.index', ['params' => $params, 'executiveId' => $executiveId]);
    }

    public function executiveUpdate(Request $request, $params, $executiveId, $id)
    {
        $validateData = $request->validate([
            'title' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            $agency = Agency::where('id', decode_params($executiveId))
                ->where('ministry_id', $ministry->id)->first();
            $executiveUnit = ExecutiveUnit::where('id', decode_params($id))
                ->where('agency_id', $agency->id)
                ->where('ministry_id', $ministry->id)
                ->first();

            $executiveUnit->update([
                'title' => $validateData['title'],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('executiveUnit.index', ['params' => $params, 'executiveId' => $executiveId]);
        }
        DB::commit();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('success_msg', 'successful')
            ->flash();

        return redirect()->route('executiveUnit.index', ['params' => $params, 'executiveId' => $executiveId]);
    }

    public function executiveDestroy($params, $executiveId, $id)
    {
        $executiveUnit = ExecutiveUnit::where('id', decode_params($id))
            ->where('agency_id', decode_params($executiveId))
            ->where('ministry_id', decode_params($params))
            ->first();
        $executiveUnit->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('executiveUnit.index', ['params' => $params, 'executiveId' => $executiveId]);
    }

    public function executiveRestore($params, $executiveId, $id)
    {
        $pid = decode_params($id);

        ExecutiveUnit::withTrashed()->whereKey($pid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('executiveUnit.index', ['params' => $params, 'executiveId' => $executiveId]);
    }
    public function assignments(Request $request, $params)
    {
        $id  = decode_params($params);
        $ministries = Ministry::where('id', $id)->get();
        $ministryId = $request->input('ministry_id', $ministries->first()?->id);

        $programs = Program::with([
            'programSubs' => fn($q) => $q->orderBy('no'),
            'programSubs.clusters' => fn($q) => $q->orderBy('no'),
        ])
            ->when($ministryId, fn($q) => $q->where('ministry_id', $ministryId))
            ->orderBy('no')
            ->get();

        $agencies = Agency::when($ministryId, fn($q) => $q->where('ministry_id', $ministryId))
            ->orderBy('no')
            ->get();

        $clusMap = AgencyAssignment::whereIn('program_id', $programs->pluck('id'))
            ->whereNotNull('cluster_id')
            ->pluck('agency_id', 'cluster_id')
            ->toArray();

        $subMap  = [];
        $progMap = [];

        foreach ($programs as $program) {
            $subAssigned = [];
            foreach ($program->programSubs as $sub) {
                $clusIds = $sub->clusters->pluck('id');
                if ($clusIds->isNotEmpty()) {
                    $distinctAgencies = $clusIds->map(fn($id) => $clusMap[$id] ?? null)->unique();
                    if ($distinctAgencies->count() === 1 && $distinctAgencies->first() !== null) {
                        $subMap[$sub->id] = $distinctAgencies->first();
                    }
                }
                $subAssigned[] = $subMap[$sub->id] ?? null;
            }

            $uniqueSubAgencies = collect($subAssigned)->unique();
            if ($program->programSubs->isNotEmpty() && $uniqueSubAgencies->count() === 1 && $uniqueSubAgencies->first() !== null) {
                $progMap[$program->id] = $uniqueSubAgencies->first();
            }
        }

        return view('content::content.agency.assignments', compact(
            'ministries',
            'ministryId',
            'programs',
            'agencies',
            'progMap',
            'subMap',
            'clusMap',
            'params'
        ));
    }

    public function saveAssignments(Request $request, $params)
    {
        $id = decode_params($params);
        $ministryId = $request->input('ministry_id', $id);
        $ministry = Ministry::find($ministryId);

        if (!$ministry) {
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('រកមិនឃើញទិន្នន័យក្រសួង!', 'បញ្ហា')
                ->flash();

            return redirect()->route('agencies.assignments', ['params' => $params]);
        }

        $rawClusAgencies = $request->input('clus_agency', []);

        DB::beginTransaction();

        try {
            $programIds = Program::where('ministry_id', $ministry->id)->pluck('id');
            $programSubIds = ProgramSub::whereIn('program_id', $programIds)->pluck('id');

            $clustersById = Cluster::with('programSub')
                ->whereIn('id', array_keys($rawClusAgencies))
                ->whereIn('program_sub_id', $programSubIds)
                ->get()
                ->keyBy('id');

            $toDeleteClusterIds = [];

            foreach ($rawClusAgencies as $clusId => $agencyId) {
                $clus = $clustersById[$clusId] ?? null;
                if (!$clus) {
                    continue;
                }

                $programId = $clus->program_id ?? $clus->programSub?->program_id;

                if (empty($agencyId)) {
                    $toDeleteClusterIds[] = $clus->id;
                } else {
                    AgencyAssignment::updateOrCreate(
                        [
                            'program_id'     => $programId,
                            'program_sub_id' => $clus->program_sub_id,
                            'cluster_id'     => $clus->id,
                        ],
                        [
                            'agency_id' => $agencyId,
                        ]
                    );
                }
            }

            if (!empty($toDeleteClusterIds)) {
                AgencyAssignment::whereIn('program_id', $programIds)
                    ->whereIn('cluster_id', $toDeleteClusterIds)
                    ->delete();
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('រក្សាទុកការកំណត់អង្គភាពថវិកាបានជោគជ័យ!', 'ជោគជ័យ')
                ->flash();

            return redirect()->route('agencies.assignments', [
                'params'      => $params,
                'ministry_id' => $ministry->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('agencies.assignments', [
                'params'      => $params,
                'ministry_id' => $ministry->id,
            ]);
        }
    }
}

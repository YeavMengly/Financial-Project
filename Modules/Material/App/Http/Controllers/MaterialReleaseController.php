<?php

namespace Modules\Material\App\Http\Controllers;

use App\DataTables\Material\InitialMaterialReleaseDataTable;
use App\DataTables\Material\MaterialReleaseDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Agency;
use App\Models\Content\ProgramSub;
use App\Models\Content\Program;
use App\Models\Content\Cluster;
use App\Models\Content\AccountSub;
use App\Models\Content\Ministry;
use App\Models\Material\MaterialEntry;
use App\Models\Material\MaterialRelease;
use App\Models\Material\Projects;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MaterialReleaseController extends Controller
{

    public function getIndex(InitialMaterialReleaseDataTable $dataTable)
    {
        // return view('maintenance.maintenance');
        return $dataTable->render('material::materialRelease.initialMaterialRelease.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(MaterialReleaseDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $agency = Agency::where('ministry_id', $ministry->id)->get();
        $materialRelease = MaterialRelease::where('ministry_id', $ministry->id)->get();
        $project = Projects::where('ministry_id', $ministry->id)->get();

        return $dataTable->render('material::materialRelease.index', [
            'params' => $params,
            'ministry' => $ministry,
            'agency' => $agency,
            'materialRelease' => $materialRelease,
            'project' => $project
        ]);
        // return view('maintenance.maintenance');
    }
    public function getByProjectSubId(Request $request)
    {
        try {
            $projectId = $request->input('project_id');
            if (empty($projectId)) {
                return response()->json([]);
            }
            // Get selected project
            $selectedProject = Projects::find($projectId);

            if (!$selectedProject) {
                return response()->json([]);
            }
            // Get projects with same stock number
            // Only return projects that have all required fields
            $subProjects = Projects::query()
                ->when(
                    !empty($selectedProject->stock_number),
                    function ($query) use ($selectedProject) {

                        $query->where(
                            'stock_number',
                            $selectedProject->stock_number
                        );
                    },
                    function ($query) use ($selectedProject) {

                        $query->where(
                            'id',
                            $selectedProject->id
                        );
                    }
                )
                ->whereNotNull('program_id')
                ->whereNotNull('program_sub_id')
                ->whereNotNull('cluster_id')
                ->whereNotNull('account_sub_id')
                ->get();
            $data = $subProjects->map(function ($item) {
                $subProjectName = !empty($item->sub_project)
                    ? $item->sub_project
                    : 'Sub-project #' . $item->id;
                $accountSubId = $item->account_sub_id ?? '';
                // Get program number
                $programNo = '';
                if (!empty($item->program_id)) {
                    $program = Program::find($item->program_id);
                    if ($program) {
                        $programNo = $program->no ?? '';
                    }
                }
                // Get program sub number
                $programSubNo = '';
                if (!empty($item->program_sub_id)) {
                    $programSub = ProgramSub::find($item->program_sub_id);
                    if ($programSub) {
                        $programSubNo = $programSub->no ?? '';
                    }
                }
                $clusterNo = '';
                if (!empty($item->cluster_id)) {

                    $cluster = Cluster::find($item->cluster_id);
                    if ($programSub) {
                        $clusterNo = $cluster->no ?? '';
                    }
                }
                // Build dropdown label
                $label = trim(
                    implode(' ', array_filter([
                        $subProjectName . ' - ',
                        'អនុគណនី' . $accountSubId,
                        'កម្មវិធីទី' . $programNo,
                        'អនុកម្មវិធីទី' . $programSubNo,
                        'ចង្កោមសកម្មភាព' . $clusterNo,
                    ]))
                );
                return [
                    'value' => (string) $item->id,
                    'label' => preg_replace(
                        '/\s+/',
                        ' ',
                        $label
                    ),
                    'program_id' => (string) $item->program_id,
                    'program_sub_id' => (string) $item->program_sub_id,
                    'cluster_id' => (string) $item->cluster_id,
                    'account_sub_id' => (string) $item->account_sub_id,
                ];
            });
            return response()->json(
                $data->values()
            );
        } catch (\Throwable $e) {

            Log::error('getByProjectId Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'project_id' => $request->input('project_id'),
            ]);

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
    public function getByItemId(Request $request)
    {
        try {
            $projectId = $request->input('project_id');
            $subProjectId = $request->input('sub_project_id');

            // Sanitize string values
            $projectId = ($projectId && $projectId !== 'null' && $projectId !== 'undefined') ? $projectId : null;
            $subProjectId = ($subProjectId && $subProjectId !== 'null' && $subProjectId !== 'undefined') ? $subProjectId : null;

            if (!$projectId && !$subProjectId) {
                return response()->json([]);
            }

            $query = MaterialEntry::query();

            if ($subProjectId) {
                // Filter by sub project if selected
                $query->where('project_sub_id', $subProjectId);
            } elseif ($projectId) {
                // Filter strictly by main project when sub-project is empty
                $query->where('project_id', $projectId);
            }

            $materials = $query->get(['id', 'p_name', 'unit', 'price']);

            return response()->json($materials);
        } catch (\Throwable $e) {
            Log::error('getByItemId Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $unitType = UnitType::where('name', '!=', 'លីត្រ')->get();

        // Fetch projects and group by stock_number to remove duplicates
        $project = Projects::where('ministry_id', $ministry->id)
            ->get()
            ->unique('stock_number');

        $MaterialEntry = MaterialEntry::where('ministry_id', $ministry->id)
        ->whereNull('deleted_at')->get();
        $Agency = Agency::where('ministry_id', $ministry->id)->get();

        return view('material::materialRelease.create', [
            'params' => $params,
            'unitType' => $unitType,
            'ministry' => $ministry,
            'project' => $project,
            'MaterialEntry' => $MaterialEntry,
            'Agency' => $Agency
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        // 1. Validate Form Inputs
        $validated = $request->validate([
            'cboProject'    => 'required|integer',
            'cboSubProject' => 'nullable',
            'agency'        => 'required|integer',
            'date_release'  => 'required|date',

            // Dynamic Item Table Arrays
            'p_name'        => 'required|array|min:1',
            'p_name.*'      => 'required|string|max:255',
            'unit'          => 'required|array|min:1',
            'unit.*'        => 'required|integer',
            'quantity'      => 'required|array|min:1',
            'quantity.*'    => 'required|numeric|min:0',
            'price'         => 'required|array|min:1',
            'price.*'       => 'required|numeric|min:0',
            'source'        => 'nullable|array',
            'source.*'      => 'nullable|string|max:255',
            'refer'        => 'nullable|array',
            'refer.*'      => 'nullable|string|max:255',
            'p_year'        => 'nullable|array',
            'p_year.*'      => 'nullable|string|max:255',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $id)->firstOrFail();
            $project  = Projects::where('id', $validated['cboProject'])->firstOrFail();
            $dateRelease = Carbon::parse($validated['date_release'])->format('Y-m-d');

            // Fetch Unit Names
            $unitIds = array_filter($validated['unit']);
            $units   = UnitType::whereIn('id', $unitIds)->pluck('name', 'id');

            // 2. Loop Through Each Item Row
            foreach ($validated['p_name'] as $index => $materialEntryId) {
                $quantityTotal   = (float) ($validated['quantity'][$index] ?? 0);
                $quantityRequest = (float) ($validated['price'][$index] ?? 0);
                $totalAmount     = $quantityTotal * $quantityRequest;
                $unitId          = $validated['unit'][$index] ?? null;

                // 1. Find the corresponding MaterialEntry record
                $materialEntry = MaterialEntry::find($materialEntryId);

                if ($materialEntry) {
                    // Check stock before decrementing
                    if ($materialEntry->qty >= $quantityTotal) {
                        $materialEntry->decrement('qty', $quantityTotal);
                        $materialEntry->update([
                            'total_price' => $materialEntry->qty * $materialEntry->price
                        ]);
                    } else {
                        throw new \Exception("បរិមាណនៅក្នុងស្តុកមិនគ្រប់គ្រាន់ (Stock insufficient for {$materialEntry->p_name})");
                    }

                    $pNameValue = $materialEntry->p_name;
                } else {
                    $pNameValue = $materialEntryId;
                }

                // 2. Create the MaterialRelease record
                MaterialRelease::create([
                    'ministry_id'      => $ministry->id,
                    'project_id'       => $project->id,
                    'project_sub_id'   => !empty($validated['cboSubProject']) ? $validated['cboSubProject'] : 0,
                    'agency_id'        => $validated['agency'], // <-- ADDED THIS FIELD
                    'p_name'           => $pNameValue,
                    'p_year'           => $validated['p_year'][$index] ?? '',
                    'title'            => $request->input('title', ''),
                    'unit'             => $units[$unitId] ?? '',
                    'quantity_total'   => $quantityTotal,
                    'quantity_request' => $quantityRequest,
                    'total'            => $totalAmount,
                    'source'           => $validated['source'][$index] ?? null,
                    'refer'           => $project->refer ?? null,
                    'date_release'     => $dateRelease,
                ]);
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('materialRelease.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Material Release Store Error: ' . $e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the specified resource.
     */
    public function show() {}

    /**
     * Show the form for editing the specified resource.
     */
    public function editRelease($params, $id)
    {
        $ministryId = decode_params($params);
        $releaseId  = is_numeric($id) ? $id : decode_params($id);

        $ministry  = Ministry::where('id', $ministryId)->firstOrFail();
        $unitType  = UnitType::where('name', '!=', 'លីត្រ')->get();

        $project = Projects::where('ministry_id', $ministry->id)
            ->get()
            ->unique('stock_number');

        $Agency = Agency::where('ministry_id', $ministry->id)->get();

        $release = MaterialRelease::where('id', $releaseId)
            ->where('ministry_id', $ministry->id)
            ->firstOrFail();

        // Added project_sub_id check to match the batch strictly
        $items = MaterialRelease::where('ministry_id', $ministry->id)
            ->where('project_id', $release->project_id)
            ->where('project_sub_id', $release->project_sub_id)
            ->where('date_release', $release->date_release)
            ->get();

        return view('material::materialRelease.edit', [
            'params'    => $params,
            'releaseId' => $id,
            'unitType'  => $unitType,
            'ministry'  => $ministry,
            'project'   => $project,
            'Agency'    => $Agency,
            'release'   => $release,
            'items'     => $items
        ]);
    }

    public function update(Request $request, $params, $id)
    {
        $ministryId = decode_params($params);
        $realId     = is_numeric($id) ? $id : decode_params($id);

        $validated = $request->validate([
            'cboProject'    => 'required',
            'cboSubProject' => 'nullable',
            'date_release'  => 'required|date',
            'p_name'        => 'required|array|min:1',
            'p_name.*'      => 'required',
            'unit'          => 'required|array|min:1',
            'unit.*'        => 'required',
            'quantity'      => 'required|array|min:1',
            'quantity.*'    => 'required|numeric|min:0',
            'price'         => 'required|array|min:1',
            'price.*'       => 'required|numeric|min:0',
            'source'        => 'nullable|array',
            'p_year'        => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $ministryId)->firstOrFail();
            $project  = Projects::where('id', $validated['cboProject'])->firstOrFail();

            // 1. Retrieve clicked record to establish original batch context
            $release = MaterialRelease::where('id', $realId)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            $subProjectId = $request->input('cboSubProject') ?: null;
            $agencyId     = $request->input('agency', $release->agency_id);

            $names      = array_values((array) $request->input('p_name', []));
            $units      = array_values((array) $request->input('unit', []));
            $quantities = array_values((array) $request->input('quantity', []));
            $prices     = array_values((array) $request->input('price', []));
            $sources    = array_values((array) $request->input('source', []));
            $pYears     = array_values((array) $request->input('p_year', []));

            // 2. Query all existing release items in this batch using matching sub_id
            $existingItems = MaterialRelease::where('ministry_id', $ministry->id)
                ->where('project_id', $release->project_id)
                ->where('project_sub_id', $release->project_sub_id)
                ->where('date_release', $release->date_release)
                ->get();

            // STEP 1: RESTORE ALL ORIGINAL QUANTITIES FIRST
            foreach ($existingItems as $oldItem) {
                $oldEntry = MaterialEntry::where('p_name', (string) $oldItem->p_name)->first();
                if ($oldEntry) {
                    $oldEntry->increment('qty', (float) $oldItem->quantity_total);
                    $oldEntry->update([
                        'total_price' => (float)$oldEntry->qty * (float)$oldEntry->price
                    ]);
                }
            }

            $unitTypes = UnitType::all()->keyBy('id');

            // STEP 2: LOOP AND VALIDATE/DEDUCT NEW QUANTITIES
            foreach ($names as $index => $itemVal) {
                $itemStr  = (string) $itemVal;
                $unitVal  = $units[$index] ?? null;
                $unitName = isset($unitTypes[$unitVal]) ? $unitTypes[$unitVal]->name : $unitVal;
                $q        = (float) ($quantities[$index] ?? 0);
                $p        = (float) ($prices[$index] ?? 0);

                $materialEntry = is_numeric($itemStr)
                    ? MaterialEntry::find($itemStr)
                    : MaterialEntry::where('p_name', $itemStr)->first();

                if ($materialEntry) {
                    if ($materialEntry->qty < $q) {
                        throw new \Exception("បរិមាណនៅក្នុងស្តុកមិនគ្រប់គ្រាន់ (Insufficient stock for {$materialEntry->p_name})");
                    }

                    $materialEntry->decrement('qty', $q);
                    $materialEntry->update([
                        'total_price' => (float)$materialEntry->qty * (float)$materialEntry->price
                    ]);

                    $pNameValue = $materialEntry->p_name;
                } else {
                    $pNameValue = $itemStr;
                }

                $payload = [
                    'ministry_id'      => $ministry->id,
                    'project_id'       => $project->id,
                    'project_sub_id'   => $subProjectId ?? 0,
                    'agency_id'        => $agencyId,
                    'p_name'           => $pNameValue,
                    'p_year'           => $pYears[$index] ?? '',
                    'unit'             => $unitName ?? '',
                    'quantity_total'   => $q,
                    'quantity_request' => $p,
                    'total'            => $q * $p,
                    'source'           => $sources[$index] ?? null,
                    'date_release'     => $request->input('date_release'),
                ];

                if (isset($existingItems[$index])) {
                    $existingItems[$index]->update($payload);
                } else {
                    MaterialRelease::create($payload);
                }
            }

            // STEP 3: DELETE REMOVED ROWS
            if ($existingItems->count() > count($names)) {
                for ($i = count($names); $i < $existingItems->count(); $i++) {
                    $existingItems[$i]->delete();
                }
            }

            DB::commit();

            return redirect()->route('materialRelease.index', $params)
                ->with('success', __('រក្សាទុកដោយជោគជ័យ'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MaterialRelease Update Error: ' . $e->getMessage(), [
                'id'    => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $ministryId = decode_params($params);
        $realId     = is_numeric($id) ? $id : decode_params($id);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $ministryId)->firstOrFail();

            // 1. Find only the specific target release item
            $release = MaterialRelease::where('id', $realId)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            // 2. Restore stock in MaterialEntry for this single item
            $materialEntry = MaterialEntry::where('p_name', (string) $release->p_name)->first();

            if ($materialEntry) {
                $materialEntry->increment('qty', (float) $release->quantity_total);
                $materialEntry->update([
                    'total_price' => (float) $materialEntry->qty * (float) $materialEntry->price,
                ]);
            }

            // 3. Delete only the selected row
            $release->delete();

            DB::commit();

            return redirect()->route('materialRelease.index', $params)
                ->with('success', __('បានលុបដោយជោគជ័យ'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MaterialRelease Delete Error: ' . $e->getMessage(), [
                'id'    => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'បញ្ហាក្នុងការលុប: ' . $e->getMessage());
        }
    }

    public function export(Request $request, $params)
    {

        return view('errors.404');
    }
}

<?php

namespace Modules\Material\App\Http\Controllers;

use App\DataTables\Material\InitialMaterialEntryDataTable;
use App\DataTables\Material\MaterialEntryDataTable;
use App\Exports\Material\MaterialEntriesExport;
use App\Http\Controllers\Controller;
use App\Models\Content\AccountSub;
use App\Models\Content\Agency;
use App\Models\Content\Cluster;
use App\Models\Content\Ministry;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use App\Models\Material\MaterialEntry;
use App\Models\Material\Projects;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialEntryController extends Controller
{

    public function getIndex(InitialMaterialEntryDataTable $dataTable)
    {
        // return view('maintenance.maintenance');
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
        $project = Projects::where('ministry_id', $ministry->id)->get()
            ->unique('stock_number');
        $program = Program::where('ministry_id', $ministry->id)->get();
        $programSub = ProgramSub::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $cluster = Cluster::where('ministry_id', $ministry->id)->get();
        return $dataTable->render('material::materialEntry.index', [
            'params' => $params,
            'ministry' => $ministry,
            'agency' => $agency,
            'materialEntry' => $materialEntry,
            'project' => $project,
            'program' => $program,
            'programSub' => $programSub,
            'accountSub' => $accountSub,
            'cluster' => $cluster,
        ]);
    }
    public function getByProjectId(Request $request)
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
    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id       = decode_params($params);
        $ministry = Ministry::where('id', $id)->firstOrFail();
        $unitType = UnitType::where('name', '!=', 'លីត្រ')->get();

        // Deduplicate projects by stock_number
        $project  = Projects::where('ministry_id', $ministry->id)
            ->get()
            ->unique('stock_number');

        $program    = Program::where('ministry_id', $ministry->id)->get();
        $programSub = ProgramSub::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $cluster    = Cluster::where('ministry_id', $ministry->id)->get();

        return view('material::materialEntry.create', [
            'params'     => $params,
            'unitType'   => $unitType,
            'ministry'   => $ministry,
            'project'    => $project,
            'program'    => $program,
            'programSub' => $programSub,
            'accountSub' => $accountSub,
            'cluster'    => $cluster,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'cboProject'    => 'required',
            'cboSubProject' => 'sometimes|nullable', // Allows null/skipped sub-projects
            'p_name'        => 'required|array',
            'p_name.*'      => 'required|string|max:255',
            'unit'          => 'required|array',
            'unit.*'        => 'required',
            'quantity'      => 'required|array',
            'quantity.*'    => 'required|numeric|min:1',
            'price'         => 'required|array',
            'price.*'       => 'required|numeric|min:0',
            'source'        => 'nullable|array',
            'p_year'        => 'nullable|array',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $id)->firstOrFail();
            $project  = Projects::where('id', $validated['cboProject'])->firstOrFail();

            // Safely extract sub_project value or fallback to null
            $subProjectId = $request->input('cboSubProject', null);

            foreach ($validated['p_name'] as $index => $name) {
                $unitId   = $validated['unit'][$index] ?? null;
                $unitType = UnitType::find($unitId);

                $qty   = (float) $validated['quantity'][$index];
                $price = (float) $validated['price'][$index];
                $total = $qty * $price;

                MaterialEntry::create([
                    'ministry_id'    => $ministry->id,
                    'project_id'     => $project->id,
                    'project_sub_id' => $subProjectId, // Matches actual DB column name
                    'program_id'     => $request->input('program_id'),
                    'program_sub_id' => $request->input('program_sub_id'),
                    'cluster_id'     => $request->input('cluster_id'),
                    'account_sub_id' => $request->input('account_sub_id'),
                    'p_name'         => $name,
                    'p_year'         => $request->p_year[$index] ?? '',
                    'unit'           => $unitType ? $unitType->name : '',
                    'qty'            => $qty,
                    'price'          => $price,
                    'total_price'    => $total,
                    'source'         => $request->source[$index] ?? null,
                ]);
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            //submit
            $action = $request->input('action');

            if ($action === 'save') {
                return redirect()->route('materialEntry.index', $params);
            }
            return redirect()->route('materialEntry.index', $params);
        } catch (\Throwable $e) {
            Log::error('materialEntry store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return back()->withInput();
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
        $ministryId = decode_params($params);
        $ministry   = Ministry::where('id', $ministryId)->firstOrFail();
        $realId     = is_numeric($id) ? $id : decode_params($id);

        $unitType = UnitType::where('name', '!=', 'លីត្រ')->get();

        // Fetch projects and keep only unique entries by stock_number
        $project  = Projects::where('ministry_id', $ministry->id)
            ->get()
            ->unique('stock_number');

        // 1. Fetch the primary entry user clicked
        $module = MaterialEntry::where('id', $realId)
            ->where('ministry_id', $ministry->id)
            ->firstOrFail();

        // 2. Query ALL records matching the same group criteria
        $query = MaterialEntry::where('ministry_id', $ministry->id)
            ->where('project_id', $module->project_id)
            ->where('program_id', $module->program_id)
            ->where('program_sub_id', $module->program_sub_id)
            ->where('cluster_id', $module->cluster_id)
            ->where('account_sub_id', $module->account_sub_id);

        if ($module->project_sub_id) {
            $query->where('project_sub_id', $module->project_sub_id);
        } else {
            $query->whereNull('project_sub_id');
        }

        $items = $query->get();

        return view('material::materialEntry.edit')
            ->with('params', $params)
            ->with('ministry', $ministry)
            ->with('unitType', $unitType)
            ->with('project', $project)
            ->with('module', $module)
            ->with('items', $items);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $ministryId = decode_params($params);
        $realId     = is_numeric($id) ? $id : decode_params($id);

        // Validate array inputs coming from the table UI
        $validated = $request->validate([
            'cboProject'    => 'required',
            'cboSubProject' => 'nullable',
            'p_name'        => 'required|array|min:1',
            'p_name.*'      => 'required|string|max:255',
            'unit'          => 'required|array|min:1',
            'unit.*'        => 'required',
            'quantity'      => 'required|array|min:1',
            'quantity.*'    => 'required|numeric|min:1',
            'price'         => 'required|array|min:1',
            'price.*'       => 'required|numeric|min:0',
            'source'        => 'nullable|array',
            'p_year'        => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $ministryId)->firstOrFail();
            $project  = Projects::where('id', $validated['cboProject'])->firstOrFail();

            // 1. Retrieve current clicked record to get initial group context
            $module = MaterialEntry::where('id', $realId)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            $subProjectId = $request->input('cboSubProject') ?: null;

            $names      = $request->input('p_name', []);
            $units      = $request->input('unit', []);
            $quantities = $request->input('quantity', []);
            $prices     = $request->input('price', []);
            $sources    = $request->input('source', []);
            $pYears     = $request->input('p_year', []);

            // 2. Query all existing records belonging to this group
            $query = MaterialEntry::where('ministry_id', $ministry->id)
                ->where('project_id', $module->project_id)
                ->where('program_id', $module->program_id)
                ->where('program_sub_id', $module->program_sub_id)
                ->where('cluster_id', $module->cluster_id)
                ->where('account_sub_id', $module->account_sub_id);

            if ($module->project_sub_id) {
                $query->where('project_sub_id', $module->project_sub_id);
            } else {
                $query->whereNull('project_sub_id');
            }

            $existingItems = $query->get();
            $unitTypes     = UnitType::all()->keyBy('id');

            // 3. Loop through submitted form rows
            foreach ($names as $index => $name) {
                $unitVal  = $units[$index] ?? null;
                $unitName = isset($unitTypes[$unitVal]) ? $unitTypes[$unitVal]->name : $unitVal;
                $q        = (float) ($quantities[$index] ?? 0);
                $p        = (float) ($prices[$index] ?? 0);

                $payload = [
                    'ministry_id'    => $ministry->id,
                    'project_id'     => $project->id,
                    'project_sub_id' => $subProjectId,
                    'program_id'     => $request->input('program_id'),
                    'program_sub_id' => $request->input('program_sub_id'),
                    'cluster_id'     => $request->input('cluster_id'),
                    'account_sub_id' => $request->input('account_sub_id'),
                    'p_name'         => $name,
                    'unit'           => $unitName,
                    'qty'            => $q,
                    'price'          => $p,
                    'total_price'    => $q * $p,
                    'source'         => $sources[$index] ?? null,
                    'p_year'         => $pYears[$index] ?? '',
                ];

                if (isset($existingItems[$index])) {
                    // Update existing record
                    $existingItems[$index]->update($payload);
                } else {
                    // Create new record for dynamically added (+) rows
                    MaterialEntry::create($payload);
                }
            }

            // 4. Delete leftover records if user deleted rows using (-) button
            if ($existingItems->count() > count($names)) {
                for ($i = count($names); $i < $existingItems->count(); $i++) {
                    $existingItems[$i]->delete();
                }
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MaterialEntry Update Error: ' . $e->getMessage(), [
                'id'    => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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

            $module = MaterialEntry::where('id', $realId)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();
            // Soft deletes matching rows (populates the deleted_at column)
            $module->delete();

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('delete_msg', 'delete')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MaterialEntry Destroy Error: ' . $e->getMessage(), [
                'id'    => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការលុប: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->back();
        }
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

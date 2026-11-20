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
        $unitType = UnitType::all();
        $materialEntry = MaterialEntry::where('ministry_id', $ministry->id)
            ->where('project_id', '!=', null)
            ->whereNull('deleted_at')
            ->select('p_name')
            ->distinct('project_id')
            ->get();
        $projectsQuery = Projects::where('ministry_id', $ministry->id)
            ->whereNull('deleted_at')
            ->get();

        $project      = $projectsQuery->unique('stock_number');
        $companies     = $projectsQuery->unique('company_name');
        $userEntries   = $projectsQuery->unique('user_entry');
        $sources       = $projectsQuery->unique('source');

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
            'companies' => $companies,
            'userEntries' => $userEntries,
            'sources' => $sources,
            'program' => $program,
            'programSub' => $programSub,
            'accountSub' => $accountSub,
            'cluster' => $cluster,
            'unitType' => $unitType,
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

            if (!$selectedProject || empty($selectedProject->stock_number)) {
                return response()->json([]);
            }
            // Get projects with same stock number
            $subProjects = Projects::query()
                ->where('stock_number', $selectedProject->stock_number)
                ->get();
            // Filter out empty sub_project entries safely
            $subProjects = $subProjects->filter(function ($item) {
                return !empty(trim((string)$item->sub_project));
            });
            // Group by trimmed sub_project text
            $uniqueSubProjects = $subProjects->groupBy(function ($item) {
                return trim((string)$item->sub_project);
            });

            $data = $uniqueSubProjects->map(function ($items, $subProjectName) {
                $firstItem = $items->first();

                // Safely fetch related program numbers if IDs exist
                $programNo    = !empty($firstItem->program_id) ? (Program::find($firstItem->program_id)->no ?? '') : '';
                $programSubNo = !empty($firstItem->program_sub_id) ? (ProgramSub::find($firstItem->program_sub_id)->no ?? '') : '';
                $clusterNo    = !empty($firstItem->cluster_id) ? (Cluster::find($firstItem->cluster_id)->no ?? '') : '';
                $accountSubNo = !empty($firstItem->account_sub_id) ? (AccountSub::find($firstItem->account_sub_id)->no ?? $firstItem->account_sub_id) : '';

                // Build dynamic text label
                $labelParts = [$subProjectName];
                if (!empty($accountSubNo))  $labelParts[] = 'អនុគណនី ' . $accountSubNo;
                if (!empty($programNo))     $labelParts[] = 'កម្មវិធីទី ' . $programNo;
                if (!empty($programSubNo))  $labelParts[] = 'អនុកម្មវិធីទី ' . $programSubNo;
                if (!empty($clusterNo))     $labelParts[] = 'ចង្កោម ' . $clusterNo;

                $label = implode(' - ', array_filter($labelParts));

                // Build Hierarchical Tree and collect flat options for unskipping support
                $hierarchy     = [];
                $categories    = [];
                $subCategories = [];
                $details       = [];

                foreach ($items as $item) {
                    $cat    = (string) trim((string) ($item->category ?? ''));
                    $subCat = (string) trim((string) ($item->sub_category ?? ''));
                    $detail = (string) trim((string) ($item->detail ?? ''));

                    // Fallbacks for blank database records
                    $cat    = $cat !== '' ? $cat : 'មិនមានទិន្នន័យ';
                    $subCat = $subCat !== '' ? $subCat : 'មិនមានទិន្នន័យ';

                    if (!isset($hierarchy[$cat])) {
                        $hierarchy[$cat] = [];
                    }
                    if (!isset($hierarchy[$cat][$subCat])) {
                        $hierarchy[$cat][$subCat] = [];
                    }
                    if ($detail !== '' && !in_array($detail, $hierarchy[$cat][$subCat])) {
                        $hierarchy[$cat][$subCat][] = $detail;
                    }

                    // Collect unique flat list values for unskip drop functionality
                    if ($cat !== '' && !in_array($cat, $categories)) {
                        $categories[] = $cat;
                    }
                    if ($subCat !== '' && !in_array($subCat, $subCategories)) {
                        $subCategories[] = $subCat;
                    }
                    if ($detail !== '' && !in_array($detail, $details)) {
                        $details[] = $detail;
                    }
                }

                return [
                    'value'          => (string) $firstItem->id,
                    'label'          => preg_replace('/\s+/', ' ', $label),
                    'program_id'     => (string) ($firstItem->program_id ?? ''),
                    'program_sub_id' => (string) ($firstItem->program_sub_id ?? ''),
                    'cluster_id'     => (string) ($firstItem->cluster_id ?? ''),
                    'account_sub_id' => (string) ($firstItem->account_sub_id ?? ''),
                    'category'       => $categories[0] ?? '', // Primary fallback for single select
                    'sub_category'   => $subCategories[0] ?? '',
                    'detail'         => $details[0] ?? '',
                    'hierarchy'      => $hierarchy,
                    'categories'     => $categories,
                    'sub_categories' => $subCategories,
                    'details'        => $details,
                ];
            });

            return response()->json($data->values());
        } catch (\Throwable $e) {
            Log::error('getByProjectId Error', [
                'message'    => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
                'project_id' => $request->input('project_id'),
            ]);

            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
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
        $unitType = UnitType::all();

        // Deduplicate projects and force strictly ASCENDING order by stock_number or id
        $project  = Projects::where('ministry_id', $ministry->id)
            ->whereNull('deleted_at')
            ->orderBy('stock_number', 'asc')
            ->get()
            ->unique('stock_number')
            ->sortBy(function ($item) {
                return $item->stock_number;
            })
            ->values();

        $program    = Program::where('ministry_id', $ministry->id)->orderBy('id', 'asc')->get();
        $programSub = ProgramSub::where('ministry_id', $ministry->id)->orderBy('id', 'asc')->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->orderBy('no', 'asc')->get();
        $cluster    = Cluster::where('ministry_id', $ministry->id)->orderBy('id', 'asc')->get();

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
            'cboProject'       => 'required',
            'cboSubProject'    => 'sometimes|nullable',
            'cboCategory'      => 'nullable',
            'cboSubCategory'   => 'nullable',
            'cboDetail'        => 'nullable',
            'sub_pro'          => 'nullable|array',
            'accountSub'       => 'nullable|array',
            'program'          => 'nullable|array',
            'cboProgramSub'    => 'nullable|array',
            'cboCluster'       => 'nullable|array',
            'p_name'           => 'required|array',
            'p_name.*'         => 'required|string|max:255',
            'unit'             => 'required|array',
            'unit.*'           => 'required',
            'quantity'         => 'required|array',
            'quantity.*'       => 'required|numeric|min:0',
            'price'            => 'required|array',
            'price.*'          => 'required|numeric|min:0',
            'source'           => 'nullable|array',
            'p_year'           => 'nullable|array',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $id)->firstOrFail();
            $project  = Projects::where('id', $validated['cboProject'])->firstOrFail();

            $categoryValue    = $request->input('cboCategory') !== null ? trim((string) $request->input('cboCategory')) : null;
            $subCategoryValue = $request->input('cboSubCategory') !== null ? trim((string) $request->input('cboSubCategory')) : null;
            $detailValue      = $request->input('cboDetail') !== null ? trim((string) $request->input('cboDetail')) : null;

            foreach ($validated['p_name'] as $index => $name) {
                $unitId   = $validated['unit'][$index] ?? null;
                $unitType = UnitType::find($unitId);

                $qty   = (float) $validated['quantity'][$index];
                $price = (float) $validated['price'][$index];
                $total = $qty * $price;

                $subProjectId   = $request->input("sub_pro.{$index}", $request->input('cboSubProject', null));
                $accountSubId   = $request->input("accountSub.{$index}", $request->input('account_sub_id'));
                $programId      = $request->input("program.{$index}", $request->input('program_id'));
                $programSubId   = $request->input("cboProgramSub.{$index}", $request->input('program_sub_id'));
                $clusterId      = $request->input("cboCluster.{$index}", $request->input('cluster_id'));

                MaterialEntry::create([
                    'ministry_id'     => $ministry->id,
                    'project_id'      => $project->id,
                    'project_sub_id'  => $subProjectId,
                    'category_id'     => $categoryValue !== '' ? $categoryValue : null,
                    'category_sub_id' => $subCategoryValue !== '' ? $subCategoryValue : null,
                    'detail'          => $detailValue !== '' ? $detailValue : null,
                    'program_id'      => $programId,
                    'program_sub_id'  => $programSubId,
                    'cluster_id'      => $clusterId,
                    'account_sub_id'  => $accountSubId,
                    'p_name'          => $name,
                    'p_year'          => $request->p_year[$index] ?? '',
                    'unit'            => $unitType ? $unitType->name : '',
                    'qty'             => $qty,
                    'price'           => $price,
                    'total_price'     => $total,
                    'source'          => $request->source[$index] ?? null,
                ]);
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();
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
            ->orderBy('stock_number', 'asc')
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

        $validated = $request->validate([
            'cboProject'       => 'required',
            'cboSubProject'    => 'nullable',
            'cboCategory'      => 'sometimes|nullable',
            'cboSubCategory'   => 'sometimes|nullable',
            'cboDetail'        => 'sometimes|nullable',
            'p_name'           => 'required|array|min:1',
            'p_name.*'         => 'required|string|max:255',
            'unit'             => 'required|array|min:1',
            'unit.*'           => 'required',
            'quantity'         => 'required|array|min:0',
            'quantity.*'       => 'required|numeric|min:0',
            'price'            => 'required|array|min:1',
            'price.*'          => 'required|numeric|min:0',
            'source'           => 'nullable|array',
            'p_year'           => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            $ministry = Ministry::where('id', $ministryId)->firstOrFail();
            $project  = Projects::where('id', $validated['cboProject'])->firstOrFail();

            // 1. Retrieve current clicked record to get initial group context
            $module = MaterialEntry::where('id', $realId)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            $subProjectId    = $request->input('cboSubProject', null);
            $categoryId      = $request->input('cboCategory', null);
            $categorySubId   = $request->input('cboSubCategory', null);
            $detailValue     = $request->input('cboDetail', null);
            $names      = $request->input('p_name', []);
            $units      = $request->input('unit', []);
            $quantities = $request->input('quantity', []);
            $prices     = $request->input('price', []);
            $sources    = $request->input('source', []);
            $pYears     = $request->input('p_year', []);

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
                    'ministry_id'     => $ministry->id,
                    'project_id'      => $project->id,
                    'project_sub_id'  => $subProjectId,
                    'category_id'     => $categoryId !== '' ? $categoryId : null,
                    'category_sub_id' => $categorySubId !== '' ? $categorySubId : null,
                    'detail'          => $detailValue !== '' ? $detailValue : null,
                    'program_id'      => $request->input('program_id'),
                    'program_sub_id'  => $request->input('program_sub_id'),
                    'cluster_id'      => $request->input('cluster_id'),
                    'account_sub_id'  => $request->input('account_sub_id'),
                    'p_name'          => $name,
                    'unit'            => $unitName,
                    'qty'             => $q,
                    'price'           => $p,
                    'total_price'     => $q * $p,
                    'source'          => $sources[$index] ?? null,
                    'p_year'          => $pYears[$index] ?? '',
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

    public function export(Request $request, $params, $projectId = null)
    {
        try {
            $ministryId = decode_params($params);
            $projectId = $projectId ?? $request->get('project');

            $query = MaterialEntry::query()
                ->leftJoin('projects as sub_proj', 'material_entries.project_sub_id', '=', 'sub_proj.id')
                // ->leftJoin('programs as pro', 'sub_proj.program_id', '=', 'pro.id')
                // ->leftJoin('program_subs as sub_pro', 'sub_proj.program_sub_id', '=', 'sub_pro.id')
                // ->leftJoin('clusters as cluster', 'sub_proj.cluster_id', '=', 'cluster.id')
                ->where('material_entries.ministry_id', $ministryId)
                ->select(
                    'material_entries.*',
                    DB::raw('COALESCE(sub_proj.sub_project, sub_proj.stock_name) as project_sub_name'),
                    // 'sub_proj.account_sub_id as sub_account',
                    // 'pro.no as sub_program',
                    // 'sub_pro.no as sub_program_sub',
                    // 'cluster.no as sub_cluster'
                );

            if (!empty($projectId)) {
                $query->where('material_entries.project_id', $projectId);
            }

            $query->orderBy('material_entries.project_sub_id', 'ASC')
                ->orderBy('material_entries.category_id', 'ASC')
                ->orderBy('material_entries.category_sub_id', 'ASC')
                ->orderBy('material_entries.detail', 'ASC')
                ->orderBy('material_entries.created_at', 'ASC');
            $data = $query->get();

            if ($data->isEmpty()) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error('មិនមានទិន្នន័យសម្រាប់នាំចូលទេ!', 'បញ្ហា')
                    ->flash();

                return redirect()->route('materialEntry.index', $params);
            }

            $selectedProject = !empty($projectId) ? Projects::find($projectId) : null;

            $export = new MaterialEntriesExport($data, $ministryId, $projectId, $selectedProject);

            return $export->export($request);
        } catch (\Throwable $e) {
            Log::error('Export Error: ' . $e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការនាំចូលទិន្នន័យ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('materialEntry.index', $params);
        }
    }
    public function storeUnitType(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:unit_types,name',
            ],
        ]);

        $unit = UnitType::create([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Unit added successfully.',
            'data' => [
                'id' => $unit->id,
                'name' => $unit->name,
            ],
        ]);
    }
}

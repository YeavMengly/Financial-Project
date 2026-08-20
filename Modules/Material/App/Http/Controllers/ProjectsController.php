<?php

namespace Modules\Material\App\Http\Controllers;

use App\DataTables\Material\InitialProjectsDataTable;
use App\DataTables\Material\ProjectDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\AccountSub;
use App\Models\Content\Cluster;
use App\Models\Content\Ministry;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use App\Models\Material\Projects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getIndex(InitialProjectsDataTable $dataTable)
    {
        return $dataTable->render('material::project.initialProject.index');
    }

    public function index(ProjectDataTable $dataTable, $params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->first();
        $program = Program::where('id', $id)->get();
        $accountSub = AccountSub::where('id', $id)->get();
        $programSub = ProgramSub::where('id', $id)->get();
        $cluster = Cluster::where('id', $id)->get();

        return $dataTable->render('material::project.index', [
            'params' => $params,
            'ministry' => $ministry,
            'program' => $program,
            'accountSub' => $accountSub,
            'programSub' => $programSub,
            'cluster' => $cluster,
        ]);
    }

    public function getByProgramId(Request $request)
    {
        if ($request->program_id) {
            $data = ProgramSub::select('id', 'no', 'decription')
                ->where('program_id', $request->program_id)
                ->get()
                ->map(function ($item) {
                    return [
                        'value' => $item->id,
                        'label' => $item->no . ' - ' . $item->decription,
                    ];
                });

            return response()->json($data);
        }

        return response()->json([]);
    }

    public function getByProgramSubId(Request $request)
    {
        if ($request->program_sub_id) {
            $data = Cluster::select('id', 'no', 'decription')
                ->where('program_sub_id', $request->program_sub_id)
                ->get()
                ->map(function ($item) {
                    return [
                        'value' => $item->id,
                        'label' => $item->no . ' - ' . $item->decription,
                    ];
                });

            return response()->json($data);
        }

        return response()->json([]);
    }

    public function editByProgramId(Request $request)
    {
        if (!$request->program_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = ProgramSub::select('id', 'no', 'decription')
            ->where('program_id', $request->program_id)
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
        }

        return response($html);
    }


    public function editByProgramSubId(Request $request)
    {
        if (!$request->program_sub_id) {
            return response('<option value="">ស្វែងរក...</option>');
        }

        $data = Cluster::select('id', 'no', 'decription')
            ->where('program_sub_id', $request->program_sub_id)
            ->get();

        $selectedId = (string) $request->selected_id;

        $html = '<option value="">ស្វែងរក...</option>';

        foreach ($data as $d) {
            $selected = ((string)$d->id === $selectedId) ? 'selected' : '';
            $html .= "<option value='{$d->id}' {$selected}>{$d->no} - {$d->decription}</option>";
        }

        return response($html);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $ministry = Ministry::where('id', $id)->firstOrFail();
        $program = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $programSub = ProgramSub::where('ministry_id', $ministry->id)->get();
        $cluster = Cluster::where('ministry_id', $ministry->id)->get();
        return view('material::project.create')
            ->with('params', $params)
            ->with('ministry', $ministry)
            ->with('program', $program)
            ->with('accountSub', $accountSub)
            ->with('programSub', $programSub)
            ->with('cluster', $cluster);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $ministryId = decode_params($params);
        $ministry = Ministry::where('id', $ministryId)->firstOrFail();

        // | Validation Rules
        $rules = [
            'stock_number' => ['required', 'string', 'max:255'],
            'stock_name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'warehouse_voucher' => ['required', 'string', 'max:255'],
            'user_entry' => ['required', 'string', 'max:255'],
            'user_receiver' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'title' => ['nullable', 'string', 'max:255'],
            // Support both single file and array of files
            'file' => ['nullable'],
            'file.*' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'note' => ['required', 'string', 'max:10000'],
            'refer' => ['required', 'string', 'max:10000'],
        ];

        // | Dynamic Table Validation
        $skipItemTable = $request->boolean('skipItemTable');
        if (!$skipItemTable) {
            $rules['sub_pro'] = ['required', 'array', 'min:1'];
            $rules['sub_pro.*'] = ['required', 'string', 'max:255'];
            $rules['accountSub'] = ['nullable', 'array'];
            $rules['accountSub.*'] = ['nullable', 'integer'];
            $rules['program'] = ['nullable', 'array'];
            $rules['program.*'] = ['nullable', 'integer'];
            $rules['cboProgramSub'] = ['nullable', 'array'];
            $rules['cboProgramSub.*'] = ['nullable', 'integer'];
            $rules['cboCluster'] = ['nullable', 'array'];
            $rules['cboCluster.*'] = ['nullable', 'integer'];
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $formattedDate = Carbon::parse($validated['date'])->format('Y-m-d');

            // | Secure File Upload Handling
            $filePaths = [];
            if ($request->hasFile('file')) {
                $path_store = 'uploads/project/' . date('Y-m-d');
                $files = $request->file('file');

                // Force into array so foreach works for both single and multiple uploads
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    if ($file->isValid()) {
                        $filePaths[] = $file->store($path_store, 'public');
                    }
                }
            }

            // Store string path or JSON array depending on count
            $filePath = match (count($filePaths)) {
                0 => null,
                1 => $filePaths[0],
                default => json_encode($filePaths),
            };

            // | Get Dynamic Table Data
            if (!$skipItemTable) {
                $subProjects = $validated['sub_pro'];
                $accountSubs = $validated['accountSub'] ?? [];
                $programs = $validated['program'] ?? [];
                $programSubs = $validated['cboProgramSub'] ?? [];
                $clusters = $validated['cboCluster'] ?? [];
            } else {
                $subProjects = [null];
                $accountSubs = [];
                $programs = [];
                $programSubs = [];
                $clusters = [];
            }

            // | Create Project Records
            foreach ($subProjects as $index => $subProject) {
                if (!$skipItemTable && (is_null($subProject) || trim($subProject) === '')) {
                    continue;
                }

                Projects::create([
                    'ministry_id' => $ministry->id,
                    'sub_project' => !$skipItemTable ? trim($subProject) : null,
                    'account_sub_id' => !$skipItemTable ? ($accountSubs[$index] ?? null) : null,
                    'program_id' => !$skipItemTable ? ($programs[$index] ?? null) : null,
                    'program_sub_id' => !$skipItemTable ? ($programSubs[$index] ?? null) : null,
                    'cluster_id' => !$skipItemTable ? ($clusters[$index] ?? null) : null,
                    'stock_number' => $validated['stock_number'],
                    'stock_name' => $validated['stock_name'],
                    'company_name' => $validated['company_name'],
                    'warehouse_voucher' => $validated['warehouse_voucher'],
                    'warehouse_owner' => 'ក្រសួងការងារ និងបណ្ដុះបណ្ដាលវិជ្ជាជីវៈ',
                    'user_entry' => $validated['user_entry'],
                    'user_receiver' => $validated['user_receiver'],
                    'date' => $formattedDate,
                    'title' => !empty($validated['title']) ? $validated['title'] : null,
                    'note' => strip_tags($validated['note']),
                    'refer' => strip_tags($validated['refer']),
                    'file' => $filePath,
                ]);
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('project.index', $params);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Project Store Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->except(['file']),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage(),
                ]);
        }
    }
    /**
     * Show the specified resource.
     */
    public function show() {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $ministryId = is_numeric($params) ? $params : decode_params($params);
        $projectId  = is_numeric($id) ? $id : decode_params($id);

        $ministry = Ministry::where('id', $ministryId)->firstOrFail();

        $module = Projects::where('id', $projectId)
            ->where('ministry_id', $ministryId)
            ->firstOrFail();
        // Fetch all rows sharing the same stock_number for this project
        $items = Projects::where('ministry_id', $ministryId)
            ->where('stock_number', $module->stock_number)
            ->get();
        $program    = Program::where('ministry_id', $ministry->id)->get();
        $accountSub = AccountSub::where('ministry_id', $ministry->id)->get();
        $programSub = ProgramSub::where('ministry_id', $ministry->id)->get();
        $cluster    = Cluster::where('ministry_id', $ministry->id)->get();

        return view('material::project.edit', [
            'ministry'   => $ministry,
            'module'     => $module,
            'params'     => $params,
            'id'         => $id,
            'program'    => $program,
            'accountSub' => $accountSub,
            'programSub' => $programSub,
            'cluster'    => $cluster,
            'items'      => $items,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $ministryId = decode_params($params);
        $ministry   = Ministry::where('id', $ministryId)->firstOrFail();

        $validated = $request->validate([
            'stock_number'      => 'required|string',
            'stock_name'        => 'required|string',
            'company_name'      => 'required|string',
            'warehouse_voucher' => 'required|string',
            'warehouse_owner'   => 'nullable|string',
            'user_entry'        => 'required|string',
            'user_receiver'     => 'required|string',
            'date'              => 'required|date',
            'title'             => 'nullable|string',
            'note'              => 'required|string',
            'refer'             => 'required|string',
            'item_id'           => 'nullable|array',
            'sub_pro'           => 'nullable|array',
            'sub_pro.*'         => 'nullable',
            'accountSub'        => 'nullable|array',
            'accountSub.*'      => 'nullable',
            'program'           => 'nullable|array',
            'program.*'         => 'nullable',
            'cboProgramSub'     => 'nullable|array',
            'cboProgramSub.*'   => 'nullable',
            'cboCluster'        => 'nullable|array',
            'cboCluster.*'      => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $projectId = is_numeric($id) ? $id : decode_params($id);

            $project = Projects::where('id', $projectId)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            $oldStockNumber = $project->stock_number;
            $date           = Carbon::parse($validated['date'])->format('Y-m-d');

            $noteContent  = trim(strip_tags($validated['note'])) === '' ? null : $validated['note'];
            $referContent = trim(strip_tags($validated['refer'])) === '' ? null : $validated['refer'];

            // Shared header data
            $baseData = [
                'stock_number'      => $validated['stock_number'],
                'stock_name'        => $validated['stock_name'],
                'company_name'      => $validated['company_name'],
                'warehouse_voucher' => $validated['warehouse_voucher'],
                'warehouse_owner'   => $validated['warehouse_owner'] ?? $project->warehouse_owner,
                'user_entry'        => $validated['user_entry'],
                'user_receiver'     => $validated['user_receiver'],
                'date'              => $date,
                'title'             => ($request->has('skip_title') || $request->has('skipTitleInput')) ? '' : ($validated['title'] ?? ''),
                'note'              => $noteContent,
                'refer'             => $referContent,
            ];

            // Safe helper to flatten nested arrays from Choices.js dropdowns
            $flattenValue = function ($value) {
                while (is_array($value)) {
                    $value = reset($value);
                }
                return (is_null($value) || trim((string)$value) === '') ? null : $value;
            };

            if (!$request->has('skipItemTable') && $request->has('sub_pro')) {
                $itemIdArray     = $request->input('item_id', []);
                $subProArray     = $request->input('sub_pro', []);
                $accountSubArray = $request->input('accountSub', []);
                $programArray    = $request->input('program', []);
                $programSubArray = $request->input('cboProgramSub', []);
                $clusterArray    = $request->input('cboCluster', []);

                // Gather all submitted IDs to know which existing rows to keep
                $submittedIds = array_filter($itemIdArray, function ($v) {
                    return !empty($v);
                });

                // Delete rows that were deleted from the UI table
                Projects::where('ministry_id', $ministry->id)
                    ->where('stock_number', $oldStockNumber)
                    ->whereNotIn('id', $submittedIds)
                    ->delete();

                foreach ($subProArray as $index => $subPro) {
                    $subProValue = $flattenValue($subPro);

                    if (is_null($subProValue)) {
                        continue;
                    }

                    $rowId = $itemIdArray[$index] ?? null;

                    $rowDetails = [
                        'sub_project'    => $subProValue,
                        'account_sub_id' => $flattenValue($accountSubArray[$index] ?? null),
                        'program_id'     => $flattenValue($programArray[$index] ?? null),
                        'program_sub_id' => $flattenValue($programSubArray[$index] ?? null),
                        'cluster_id'     => $flattenValue($clusterArray[$index] ?? null),
                    ];

                    if (!empty($rowId)) {
                        // Update existing row (Keeps original ID)
                        Projects::where('id', $rowId)->update(array_merge($baseData, $rowDetails));
                    } else {
                        // Create NEW row (Assigns NEW auto-increment ID)
                        Projects::create(array_merge($baseData, [
                            'ministry_id' => $ministry->id,
                        ], $rowDetails));
                    }
                }
            } else {
                $project->update(array_merge($baseData, [
                    'sub_project'    => null,
                    'account_sub_id' => null,
                    'program_id'     => null,
                    'program_sub_id' => null,
                    'cluster_id'     => null,
                ]));
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('project.index', ['params' => $params]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project update error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error('បញ្ហាក្នុងការកែប្រែ: ' . $e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        try {

            // Decode ministry ID
            $ministryId = decode_params($params);

            // Decode project ID
            $projectId = decode_params($id);

            // Find project belonging to this ministry
            $module = Projects::where('id', $projectId)
                ->where('ministry_id', $ministryId)
                ->first();

            if (!$module) {

                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(
                        'Project not found.',
                        'Error'
                    )
                    ->flash();

                return redirect()->route('project.index', [
                    'params' => $params,
                ]);
            }

            // Soft delete
            $module->delete();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success(
                    'delete_msg',
                    'delete'
                )
                ->flash();

            return redirect()->route('project.index', [
                'params' => $params,
            ]);
        } catch (\Throwable $e) {

            Log::error('Project delete failed', [
                'params' => $params,
                'id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            flash()
                ->translate('en')
                ->option('timeout', 3000)
                ->error(
                    'Unable to delete project: ' . $e->getMessage(),
                    'Error'
                )
                ->flash();

            return redirect()->route('project.index', [
                'params' => $params,
            ]);
        }
    }

    public function restore($params, $id)
    {
        $aid = decode_params($id);

        Projects::withTrashed()->whereKey($aid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('project.index', $params);
    }
}

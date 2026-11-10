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
use Carbon\Carbon;


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
    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request, $params)
    {
        $ministryId = decode_params($params);
        $ministry   = Ministry::where('id', $ministryId)->firstOrFail();

        // | Validation Rules
        $rules = [
            'stock_number'      => ['required', 'string', 'max:255'],
            'stock_name'        => ['required', 'string', 'max:255'],
            'company_name'      => ['required', 'string', 'max:255'],
            'warehouse_voucher' => ['required', 'string', 'max:255'],
            'user_entry'        => ['required', 'string', 'max:255'],
            'user_receiver'     => ['required', 'string', 'max:255'],
            'date'              => ['required', 'date'],
            'title'             => ['nullable', 'string', 'max:255'],
            'file'              => ['nullable'],
            'file.*'            => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'note'              => ['required', 'string', 'max:10000'],
            'refer'             => ['required', 'string', 'max:10000'],
        ];

        // | Dynamic Table Validation
        $skipItemTable = $request->boolean('skipItemTable');
        if (!$skipItemTable) {
            $rules['sub_pro']          = ['required', 'array', 'min:1'];
            $rules['sub_pro.*']        = ['required', 'string', 'max:255'];
            $rules['accountSub']       = ['nullable', 'array'];
            $rules['accountSub.*']     = ['nullable'];
            $rules['category']         = ['nullable', 'array'];
            $rules['category.*']       = ['nullable', 'array'];
            $rules['category.*.*']     = ['nullable', 'string', 'max:255'];
            $rules['sub_category']     = ['nullable', 'array'];
            $rules['sub_category.*']   = ['nullable', 'array'];
            $rules['sub_category.*.*'] = ['nullable', 'string', 'max:255'];
            $rules['program']          = ['nullable', 'array'];
            $rules['program.*']        = ['nullable'];
            $rules['cboProgramSub']    = ['nullable', 'array'];
            $rules['cboProgramSub.*']  = ['nullable'];
            $rules['cboCluster']       = ['nullable', 'array'];
            $rules['cboCluster.*']     = ['nullable'];
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $formattedDate = Carbon::parse($validated['date'])->format('Y-m-d');

            // | Secure File Upload Handling
            $filePaths = [];
            if ($request->hasFile('file')) {
                $path_store = 'uploads/project/' . date('Y-m-d');
                $files      = $request->file('file');

                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    if ($file->isValid()) {
                        $filePaths[] = $file->store($path_store, 'public');
                    }
                }
            }

            $filePath = match (count($filePaths)) {
                0       => null,
                1       => $filePaths[0],
                default => json_encode($filePaths),
            };

            // | Get Dynamic Table Data
            if (!$skipItemTable) {
                $subProjects        = $validated['sub_pro'] ?? [];
                $accountSubs        = $validated['accountSub'] ?? [];
                $categoriesGroup    = $validated['category'] ?? [];
                $subCategoriesGroup = $validated['sub_category'] ?? [];
                $programs           = $validated['program'] ?? [];
                $programSubs        = $validated['cboProgramSub'] ?? [];
                $clusters           = $validated['cboCluster'] ?? [];
            } else {
                $subProjects        = [null];
                $accountSubs        = [];
                $categoriesGroup    = [];
                $subCategoriesGroup = [];
                $programs           = [];
                $programSubs        = [];
                $clusters           = [];
            }

            // Helper to safely extract single scalar values from inputs
            $flattenValue = function ($val) {
                while (is_array($val)) {
                    $val = reset($val);
                }
                return (is_null($val) || trim((string)$val) === '') ? null : $val;
            };

            // Base shared data across all created rows for this submission
            $baseRecordData = [
                'ministry_id'       => $ministry->id,
                'stock_number'      => $validated['stock_number'],
                'stock_name'        => $validated['stock_name'],
                'company_name'      => $validated['company_name'],
                'warehouse_voucher' => $validated['warehouse_voucher'],
                'warehouse_owner'   => 'ក្រសួងការងារ និងបណ្ដុះបណ្ដាលវិជ្ជាជីវៈ',
                'user_entry'        => $validated['user_entry'],
                'user_receiver'     => $validated['user_receiver'],
                'date'              => $formattedDate,
                'title'             => !empty($validated['title']) ? $validated['title'] : null,
                'note'              => strip_tags($validated['note']),
                'refer'             => strip_tags($validated['refer']),
                'file'              => $filePath,
            ];

            // | Create Project Records (Separate row for each category entry)
            foreach ($subProjects as $index => $subProject) {
                if (!$skipItemTable && (is_null($subProject) || trim((string)$subProject) === '')) {
                    continue;
                }

                // Filter out empty elements for this row
                $rowCategories = isset($categoriesGroup[$index]) && is_array($categoriesGroup[$index])
                    ? array_values(array_filter($categoriesGroup[$index], fn($v) => !is_null($v) && trim((string)$v) !== ''))
                    : [];

                $rowSubCategories = isset($subCategoriesGroup[$index]) && is_array($subCategoriesGroup[$index])
                    ? array_values(array_filter($subCategoriesGroup[$index], fn($v) => !is_null($v) && trim((string)$v) !== ''))
                    : [];

                $rowSpecificData = [
                    'sub_project'    => !$skipItemTable ? trim((string)$subProject) : null,
                    'account_sub_id' => !$skipItemTable ? $flattenValue($accountSubs[$index] ?? null) : null,
                    'program_id'     => !$skipItemTable ? $flattenValue($programs[$index] ?? null) : null,
                    'program_sub_id' => !$skipItemTable ? $flattenValue($programSubs[$index] ?? null) : null,
                    'cluster_id'     => !$skipItemTable ? $flattenValue($clusters[$index] ?? null) : null,
                ];

                if (empty($rowCategories)) {
                    // If no categories were filled, create a single row with null categories
                    Projects::create(array_merge($baseRecordData, $rowSpecificData, [
                        'category'     => null,
                        'sub_category' => null,
                    ]));
                } else {
                    // Loop through each category input and create a separate database row
                    foreach ($rowCategories as $catIdx => $categoryItem) {
                        $subCategoryItem = $rowSubCategories[$catIdx] ?? null;

                        Projects::create(array_merge($baseRecordData, $rowSpecificData, [
                            'category'     => $categoryItem,
                            'sub_category' => $subCategoryItem,
                        ]));
                    }
                }
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
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
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

        // Fetch all rows sharing the same stock_number and group/merge their categories
        $items = Projects::where('ministry_id', $ministryId)
            ->where('stock_number', $module->stock_number)
            ->get()
            ->groupBy('sub_project')
            ->map(function ($rows) {
                $first = $rows->first();
                // Collect and merge all category and sub_category values across rows into single flat arrays
                $first->category = $rows->flatMap(function ($row) {
                    return is_array($row->category) ? $row->category : json_decode($row->category, true) ?? [$row->category];
                })->filter()->values()->all();

                $first->sub_category = $rows->flatMap(function ($row) {
                    return is_array($row->sub_category) ? $row->sub_category : json_decode($row->sub_category, true) ?? [$row->sub_category];
                })->filter()->values()->all();

                return $first;
            })
            ->values();

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
     * Update the specified project in storage without deleting any rows.
     */

    /**
     * Update the specified project in storage.
     */
    /**
     * Update the specified project in storage.
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
            'note'              => 'required|string',
            'refer'             => 'required|string',
            'item_id'           => 'nullable|array',
            'item_id.*'         => 'nullable',
            'sub_pro'           => 'nullable|array',
            'sub_pro.*'         => 'nullable',
            'category'          => 'nullable|array',
            'category.*'        => 'nullable|array',
            'category.*.*'      => 'nullable|string',
            'sub_category'      => 'nullable|array',
            'sub_category.*'    => 'nullable|array',
            'sub_category.*.*'  => 'nullable|string',
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

            // Shared header details
            $baseData = [
                'stock_number'      => $validated['stock_number'],
                'stock_name'        => $validated['stock_name'],
                'company_name'      => $validated['company_name'],
                'warehouse_voucher' => $validated['warehouse_voucher'],
                'warehouse_owner'   => $validated['warehouse_owner'] ?? $project->warehouse_owner,
                'user_entry'        => $validated['user_entry'],
                'user_receiver'     => $validated['user_receiver'],
                'date'              => $date,
                'note'              => $noteContent,
                'refer'             => $referContent,
            ];

            $flattenValue = function ($value) {
                while (is_array($value)) {
                    $value = reset($value);
                }
                return (is_null($value) || trim((string)$value) === '') ? null : $value;
            };

            $skipItemTable = $request->boolean('skipItemTable');

            if ($skipItemTable) {
                // Remove all other child records if table is skipped
                Projects::where('ministry_id', $ministry->id)
                    ->where('stock_number', $oldStockNumber)
                    ->where('id', '!=', $project->id)
                    ->forceDelete(); // Use forceDelete() if using SoftDeletes, or delete()

                $project->update(array_merge($baseData, [
                    'sub_project'    => null,
                    'category'       => null,
                    'sub_category'   => null,
                    'account_sub_id' => null,
                    'program_id'     => null,
                    'program_sub_id' => null,
                    'cluster_id'     => null,
                ]));
            } else {
                $subProArray      = $request->input('sub_pro', []);
                $categoriesArray  = $request->input('category', []);
                $subCategoriesArr = $request->input('sub_category', []);
                $accountSubArray  = $request->input('accountSub', []);
                $programArray     = $request->input('program', []);
                $programSubArray  = $request->input('cboProgramSub', []);
                $clusterArray     = $request->input('cboCluster', []);

                // Get all active IDs linked to this stock number before updating
                $existingDbIds = Projects::where('ministry_id', $ministry->id)
                    ->where('stock_number', $oldStockNumber)
                    ->pluck('id')
                    ->toArray();

                // Store array of IDs that were kept/updated or created in this submission
                $savedIds = [];

                foreach ($subProArray as $index => $subPro) {
                    $subProValue = $flattenValue($subPro);
                    if (is_null($subProValue)) {
                        continue;
                    }

                    $rowCategories = isset($categoriesArray[$index]) && is_array($categoriesArray[$index])
                        ? array_values(array_filter($categoriesArray[$index], fn($val) => !is_null($val) && trim((string)$val) !== ''))
                        : [];

                    $rowSubCategories = isset($subCategoriesArr[$index]) && is_array($subCategoriesArr[$index])
                        ? array_values(array_filter($subCategoriesArr[$index], fn($val) => !is_null($val) && trim((string)$val) !== ''))
                        : [];

                    $rowDetails = [
                        'sub_project'    => $subProValue,
                        'account_sub_id' => $flattenValue($accountSubArray[$index] ?? null),
                        'program_id'     => $flattenValue($programArray[$index] ?? null),
                        'program_sub_id' => $flattenValue($programSubArray[$index] ?? null),
                        'cluster_id'     => $flattenValue($clusterArray[$index] ?? null),
                    ];

                    if (empty($rowCategories)) {
                        $targetId = array_shift($existingDbIds);

                        if ($targetId && $existing = Projects::find($targetId)) {
                            $existing->update(array_merge($baseData, $rowDetails, [
                                'category'     => null,
                                'sub_category' => null,
                            ]));
                            $savedIds[] = $existing->id;
                        } else {
                            $newRecord = Projects::create(array_merge($baseData, ['ministry_id' => $ministry->id], $rowDetails, [
                                'category'     => null,
                                'sub_category' => null,
                            ]));
                            $savedIds[] = $newRecord->id;
                        }
                    } else {
                        foreach ($rowCategories as $catIdx => $categoryItem) {
                            $subCategoryItem = $rowSubCategories[$catIdx] ?? null;
                            $targetId        = array_shift($existingDbIds);

                            if ($targetId && $existing = Projects::find($targetId)) {
                                $existing->update(array_merge($baseData, $rowDetails, [
                                    'category'     => $categoryItem,
                                    'sub_category' => $subCategoryItem,
                                ]));
                                $savedIds[] = $existing->id;
                            } else {
                                $newRecord = Projects::create(array_merge($baseData, ['ministry_id' => $ministry->id], $rowDetails, [
                                    'category'     => $categoryItem,
                                    'sub_category' => $subCategoryItem,
                                ]));
                                $savedIds[] = $newRecord->id;
                            }
                        }
                    }
                }

                // Delete any remaining database IDs under this stock number that were removed in the UI
                Projects::where('ministry_id', $ministry->id)
                    ->where('stock_number', $oldStockNumber)
                    ->whereNotIn('id', $savedIds)
                    ->forceDelete(); // Use ->forceDelete() to permanently erase rows instead of soft-deleting
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
                'line' => $e->getLine(),
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

    private function getProjectFile($id): array
    {
        $project = Projects::findOrFail($id);

        // Apply your project's authorization rules here.
        abort_unless(hasPermission('project.index'), 403);

        $path = $project->file;

        abort_if(empty($path), 404);

        // Accept only paths inside the project upload directory.
        abort_unless(
            str_starts_with($path, 'uploads/project/')
                && !str_contains($path, '..')
                && !str_contains($path, '\\'),
            404
        );

        // New files: storage/app/uploads/...
        if (Storage::disk('local')->exists($path)) {
            return [$project, 'local', $path];
        }

        // Legacy files: public/uploads/...
        if (Storage::disk('public')->exists($path)) {
            return [$project, 'public', $path];
        }

        abort(404, 'File not found.');
    }

    public function preview($id)
    {
        [$project, $disk, $path] = $this->getProjectFile($id);

        abort_unless(
            strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf',
            415,
            'Only PDF files can be previewed.'
        );

        return Storage::disk($disk)->response(
            $path,
            basename($path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' =>
                'inline; filename="' .
                    addcslashes(basename($path), '"\\') . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function download($id)
    {
        [$project, $disk, $path] = $this->getProjectFile($id);

        return Storage::disk($disk)->download(
            $path,
            basename($path)
        );
    }
}

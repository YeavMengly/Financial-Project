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
        // $project = Projects::where('ministry_id', $ministry->id)
        //     ->distinct('stock_number')
        //     ->get();

        $projectsQuery = Projects::where('ministry_id', $ministry->id)
            ->whereNull('deleted_at')
            ->get();

        $project      = $projectsQuery->unique('stock_number'); // Or unique('id')
        $companies     = $projectsQuery->unique('company_name');
        $userEntries   = $projectsQuery->unique('user_entry');
        $sources       = $projectsQuery->unique('source');
        return $dataTable->render('material::materialRelease.index', [
            'params' => $params,
            'ministry' => $ministry,
            'agency' => $agency,
            'materialRelease' => $materialRelease,
            'project' => $project,
            'companies' => $companies,
            'userEntries' => $userEntries,
            'sources' => $sources
        ]);
    }

    public function getByProjectSubId(Request $request)
    {
        try {
            $projectId = $request->input('project_id');
            if (empty($projectId)) {
                return response()->json([]);
            }

            $selectedProject = Projects::find($projectId);

            if (!$selectedProject) {
                return response()->json([]);
            }

            $subProjects = Projects::query()
                ->when(
                    !empty($selectedProject->stock_number),
                    function ($query) use ($selectedProject) {
                        $query->where('stock_number', $selectedProject->stock_number);
                    },
                    function ($query) use ($selectedProject) {
                        $query->where('id', $selectedProject->id);
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

                $programNo = '';
                if (!empty($item->program_id)) {
                    $program = Program::find($item->program_id);
                    if ($program) {
                        $programNo = $program->no ?? '';
                    }
                }

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
                    if ($cluster) {
                        $clusterNo = $cluster->no ?? '';
                    }
                }

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
                    'label' => preg_replace('/\s+/', ' ', $label),
                    'program_id' => (string) $item->program_id,
                    'program_sub_id' => (string) $item->program_sub_id,
                    'cluster_id' => (string) $item->cluster_id,
                    'account_sub_id' => (string) $item->account_sub_id,
                ];
            });

            return response()->json($data->values());
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

    // public function getByItemId(Request $request)
    // {
    //     try {
    //         $projectId = $request->input('project_id');
    //         $subProjectId = $request->input('sub_project_id');
    //         $releaseId = $request->input('release_id'); // Pass release_id if editing

    //         $projectId = ($projectId && $projectId !== 'null' && $projectId !== 'undefined') ? $projectId : null;
    //         $subProjectId = ($subProjectId && $subProjectId !== 'null' && $subProjectId !== 'undefined') ? $subProjectId : null;

    //         if (!$projectId && !$subProjectId) {
    //             return response()->json([]);
    //         }

    //         $query = MaterialEntry::query();

    //         if ($subProjectId) {
    //             $query->where('project_sub_id', $subProjectId);
    //         } elseif ($projectId) {
    //             $query->where('project_id', $projectId);
    //         }

    //         // Include items that either have stock (> 0) OR are part of the current edit batch
    //         $query->where(function ($q) use ($releaseId) {
    //             $q->where('qty', '>', 0);

    //             if ($releaseId) {
    //                 $release = MaterialRelease::find($releaseId);
    //                 if ($release) {
    //                     $existingNames = MaterialRelease::where('ministry_id', $release->ministry_id)
    //                         ->where('project_id', $release->project_id)
    //                         ->where('project_sub_id', $release->project_sub_id)
    //                         ->where('date_release', $release->date_release)
    //                         ->pluck('p_name')
    //                         ->toArray();

    //                     $q->orWhereIn('p_name', $existingNames);
    //                 }
    //             }
    //         });

    //         $materials = $query->get(['id', 'p_name', 'unit', 'price', 'qty']);

    //         return response()->json($materials);
    //     } catch (\Throwable $e) {
    //         Log::error('getByItemId Error: ' . $e->getMessage());
    //         return response()->json([], 500);
    //     }
    // }
    public function getByItemId(Request $request)
    {
        try {
            $projectId    = $request->input('project_id');
            $subProjectId = $request->input('sub_project_id');
            $releaseId = $request->input('release_id');

            $projectId    = ($projectId && $projectId !== 'null' && $projectId !== 'undefined') ? $projectId : null;
            $subProjectId = ($subProjectId && $subProjectId !== 'null' && $subProjectId !== 'undefined') ? $subProjectId : null;

            if (!$projectId && !$subProjectId) {
                return response()->json([]);
            }

            $query = MaterialEntry::query();

            if ($subProjectId) {
                $query->where('project_sub_id', $subProjectId);
            } elseif ($projectId) {
                $query->where('project_id', $projectId);
            }

            // Get entry IDs for the current edit batch to preserve them during updates
            $existingEntryIds = [];
            if ($releaseId) {
                $release = MaterialRelease::find($releaseId);
                if ($release) {
                    $existingEntryIds = MaterialRelease::where('ministry_id', $release->ministry_id)
                        ->where('project_id', $release->project_id)
                        ->where('project_sub_id', $release->project_sub_id)
                        ->where('date_release', $release->date_release)
                        ->pluck('material_entry_id')
                        ->filter()
                        ->toArray();
                }
            }

            // Check if entry qty is greater than total released quantity
            $query->where(function ($q) use ($existingEntryIds) {
                $q->whereRaw('qty > (select coalesce(sum(quantity_request), 0) from material_releases where material_releases.material_entry_id = material_entries.id)');

                if (!empty($existingEntryIds)) {
                    $q->orWhereIn('id', $existingEntryIds);
                }
            });

            // Filter out out-of-stock items dynamically
            $filteredMaterials = $materials->filter(function ($entry) use ($excludedReleaseIds) {
                $otherReleasesTotal = MaterialRelease::where('p_name', $entry->p_name)
                    ->where('ministry_id', $entry->ministry_id)
                    ->when(!empty($excludedReleaseIds), function ($q) use ($excludedReleaseIds) {
                        $q->whereNotIn('id', $excludedReleaseIds);
                    })
                    ->sum('quantity_total');

                $remainingStock = $entry->qty - $otherReleasesTotal;

                return $remainingStock > 0;
            });

            return response()->json($filteredMaterials->values());
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
        $id       = decode_params($params);
        $ministry = Ministry::where('id', $id)->firstOrFail();
        $unitType = UnitType::where('name', '!=', 'លីត្រ')->get();

        $project = Projects::where('ministry_id', $ministry->id)
            ->get()
            ->unique('stock_number');

        // Filter material entries: only keep items with dynamic stock > 0
        $MaterialEntry = MaterialEntry::where('ministry_id', $ministry->id)
            ->whereNull('deleted_at')->get();

        $Agency = Agency::where('ministry_id', $ministry->id)->get();

        return view('material::materialRelease.create', [
            'params'        => $params,
            'unitType'      => $unitType,
            'ministry'      => $ministry,
            'project'       => $project,
            'MaterialEntry' => $MaterialEntry,
            'Agency'        => $Agency
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request, $params)
    // {
    //     $validated = $request->validate([
    //         'cboProject'    => 'required|integer',
    //         'cboSubProject' => 'nullable',
    //         'agency'        => 'required|integer',
    //         'date_release'  => 'required|date',

    //         'p_name'        => 'required|array|min:1',
    //         'p_name.*'      => 'required|string|max:255',
    //         'unit'          => 'required|array|min:1',
    //         'unit.*'        => 'required|integer',
    //         'quantity'      => 'required|array|min:1',
    //         'quantity.*'    => 'required|numeric|min:0',
    //         'price'         => 'required|array|min:1',
    //         'price.*'       => 'required|numeric|min:0',
    //         'source'        => 'nullable|array',
    //         'source.*'      => 'nullable|string|max:255',
    //         'refer'         => 'nullable|array',
    //         'refer.*'       => 'nullable|string|max:255',
    //         'p_year'        => 'nullable|array',
    //         'p_year.*'      => 'nullable|string|max:255',
    //     ]);

    //     $id = decode_params($params);
    //     DB::beginTransaction();

    //     try {
    //         $ministry = Ministry::where('id', $id)->firstOrFail();
    //         $project  = Projects::where('id', $validated['cboProject'])->firstOrFail();
    //         $dateRelease = Carbon::parse($validated['date_release'])->format('Y-m-d');

    //         $unitIds = array_filter($validated['unit']);
    //         $units   = UnitType::whereIn('id', $unitIds)->pluck('name', 'id');

    //         foreach ($validated['p_name'] as $index => $materialEntryId) {
    //             $quantity   = $validated['quantity'][$index];
    //             $price = (float) ($validated['price'][$index] ?? 0);
    //             $totalAmount     = $quantity * $price;
    //             $unitId          = $validated['unit'][$index] ?? null;

    //             $materialEntry = MaterialEntry::find($materialEntryId);

    //             $pNameValue = $materialEntry->p_name;
    //             $this->recalculateLedger($ministry->id, $validated['cboProject'], $materialEntry->p_name);

    //             // if ($materialEntry) {
    //             //     if ($materialEntry->qty >= $quantity) {
    //             //         $materialEntry->decrement('qty', $quantity);

    //             //         // Recalculate total_price for updated stock quantity
    //             //         $materialEntry->update([
    //             //             'total_price' => $materialEntry->qty * $materialEntry->price
    //             //         ]);
    //             //     } else {
    //             //         throw new \Exception("បរិមាណនៅក្នុងស្តុកមិនគ្រប់គ្រាន់ (Stock insufficient for {$materialEntry->p_name})");
    //             //     }

    //             //     $pNameValue = $materialEntry->p_name;
    //             // } else {
    //             //     $pNameValue = $materialEntryId;
    //             // }

    //             MaterialRelease::create([
    //                 'ministry_id'      => $ministry->id,
    //                 'project_id'       => $project->id,
    //                 'project_sub_id'   => !empty($validated['cboSubProject']) ? $validated['cboSubProject'] : 0,
    //                 'agency_id'        => $validated['agency'],
    //                 'p_name'           => $pNameValue,
    //                 'p_year'           => $validated['p_year'][$index] ?? '',
    //                 'title'            => $request->input('title', ''),
    //                 'unit'             => $units[$unitId] ?? '',
    //                 'quantity_total'   => $materialEntry->qty,
    //                 'quantity_request' => $quantity,
    //                 'price'            => $price,
    //                 'total_price'      => $totalAmount,
    //                 'source'           => $validated['source'][$index] ?? null,
    //                 'refer'            => $project->refer ?? null,
    //                 'date_release'     => $dateRelease,
    //             ]);
    //         }

    //         DB::commit();

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->success('success_msg', 'successful')
    //             ->flash();

    //         return redirect()->route('materialRelease.index', $params);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Material Release Store Error: ' . $e->getMessage());

    //         flash()
    //             ->translate('en')
    //             ->option('timeout', 2000)
    //             ->error('បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(), 'បញ្ហា')
    //             ->flash();

    //         return redirect()->back()->withInput();
    //     }
    // }
    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'cboProject'    => 'required|integer',
            'cboSubProject' => 'nullable',
            'agency'        => 'required|integer',
            'date_release'  => 'required|date',
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

            'refer'         => 'nullable|array',
            'refer.*'       => 'nullable|string|max:255',

            'p_year'        => 'nullable|array',
            'p_year.*'      => 'nullable|string|max:255',
        ]);

        $id = decode_params($params);

        DB::beginTransaction();

        try {

            $ministry = Ministry::where('id', $id)->firstOrFail();

            $project = Projects::where('id', $validated['cboProject'])
                ->firstOrFail();

            $dateRelease = Carbon::parse(
                $validated['date_release']
            )->format('Y-m-d');

            $unitIds = array_filter($validated['unit']);

            $units = UnitType::whereIn('id', $unitIds)
                ->pluck('name', 'id');

            foreach ($validated['p_name'] as $index => $materialEntryId) {

                /*
            |--------------------------------------------------------------------------
            | 1. Find MaterialEntry
            |--------------------------------------------------------------------------
            */

                $materialEntry = MaterialEntry::where('id', $materialEntryId)
                    ->where('ministry_id', $ministry->id)
                    ->where('project_id', $project->id)
                    ->first();

                if (!$materialEntry) {
                    throw new \Exception(
                        'មិនអាចរកឃើញសម្ភារៈក្នុងស្តុកទេ!'
                    );
                }

                $pNameValue = $materialEntry->p_name;

                /*
            |--------------------------------------------------------------------------
            | 2. Original stock quantity
            |--------------------------------------------------------------------------
            */

                $stockQuantity = (float) $materialEntry->qty;

                /*
            |--------------------------------------------------------------------------
            | 3. Get already released quantity
            |--------------------------------------------------------------------------
            */

                $alreadyReleased = MaterialRelease::where(
                    'ministry_id',
                    $ministry->id
                )
                    ->where('project_id', $project->id)
                    ->where('p_name', $pNameValue)
                    ->sum('quantity_request');

                $alreadyReleased = (float) $alreadyReleased;

                /*
            |--------------------------------------------------------------------------
            | 4. New request quantity
            |--------------------------------------------------------------------------
            */

                $quantity = (float) $validated['quantity'][$index];

                /*
            |--------------------------------------------------------------------------
            | 5. Calculate total requested
            |--------------------------------------------------------------------------
            */

                $totalRequested = $alreadyReleased + $quantity;

                /*
            |--------------------------------------------------------------------------
            | 6. Check stock
            |--------------------------------------------------------------------------
            */

                if ($totalRequested > $stockQuantity) {

                    $remaining = $stockQuantity - $alreadyReleased;

                    throw new \Exception(
                        "ស្តុកមិនគ្រប់សម្រាប់ {$pNameValue}! " .
                            "ចំនួនសរុបមាន: {$stockQuantity}, " .
                            "បានបញ្ចេញរួច: {$alreadyReleased}, " .
                            "នៅសល់អាចបញ្ចេញបាន: {$remaining}, " .
                            "ចំនួនស្នើសុំ: {$quantity}"
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | 7. Price
            |--------------------------------------------------------------------------
            */

                $price = (float) (
                    $validated['price'][$index] ?? 0
                );

                $totalAmount = $quantity * $price;

                $unitId = $validated['unit'][$index] ?? null;

                /*
            |--------------------------------------------------------------------------
            | 8. Create MaterialRelease
            |--------------------------------------------------------------------------
            */

                MaterialRelease::create([
                    'ministry_id'      => $ministry->id,
                    'project_id'       => $project->id,
                    'material_entry_id' => $materialEntry->id,

                    'project_sub_id'   => !empty($validated['cboSubProject'])
                        ? $validated['cboSubProject']
                        : 0,

                    'agency_id'        => $validated['agency'],

                    'p_name'           => $pNameValue,

                    'p_year'           => $validated['p_year'][$index] ?? '',

                    'title'            => $request->input('title', ''),

                    'unit'             => $units[$unitId] ?? '',

                    'quantity_total'   => $stockQuantity,

                    'quantity_request' => $quantity,

                    'price'            => $price,

                    'total_price'      => $totalAmount,

                    'source'           => $validated['source'][$index] ?? null,

                    'refer'            => $project->refer ?? null,

                    'date_release'    => $dateRelease,
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | 9. Recalculate ledger after ALL releases are inserted
        |--------------------------------------------------------------------------
        */

            foreach ($validated['p_name'] as $index => $materialEntryId) {

                $materialEntry = MaterialEntry::findOrFail(
                    $materialEntryId
                );

                $this->recalculateLedger(
                    $ministry->id,
                    $project->id,
                    $materialEntry->p_name
                );
            }

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success(
                    'success_msg',
                    'successful'
                )
                ->flash();

            return redirect()->route(
                'materialRelease.index',
                $params
            );
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Material Release Store Error: ' . $e->getMessage(),
                [
                    'trace' => $e->getTraceAsString()
                ]
            );

            flash()
                ->translate('en')
                ->option('timeout', 3000)
                ->error(
                    'បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage(),
                    'បញ្ហា'
                )
                ->flash();

            return back()->withInput();
        }
    }


    // private function recalculateLedger($ministryId, $cboProject, $p_name)
    // {
    //     // Find initial stock quantity from DuelEntry
    //     $materialEntry = MaterialEntry::where('ministry_id', $ministryId)
    //         ->where('project_id', $cboProject)
    //         ->where('p_name', $p_name)
    //         ->firstOrFail();
    //     $runningBalance = $materialEntry ? ($materialEntry->qty ?? 0) : 0;

    //     // Fetch all releases for this specific stock item ordered by ID (creation order)
    //     $releases = MaterialRelease::where('ministry_id', $ministryId)
    //         ->where('project_id', $cboProject)
    //         ->where('p_name', $p_name)
    //         ->orderBy('date_release', 'ASC')
    //         ->get();

    //     foreach ($releases as $release) {
    //         $quantity = $runningBalance;
    //         $materialTotal = $quantity - $release->quantity_request;

    //         // Enforce stock non-negativity constraint across the entire timeline
    //         if ($materialTotal < 0) {
    //             throw new \Exception(' ទិន្នន័យមិនគ្រប់ចំនួន សូមពិនិត្យម្តងទៀត!');
    //         }

    //         $release->update([
    //             'quantity_total' => $quantity,
    //         ]);

    //         // Pass updated balance down to the next row in the sequence
    //         $runningBalance = $materialTotal;
    //     }
    // }
    private function recalculateLedger(
        $ministryId,
        $cboProject,
        $p_name
    ) {
        /*
    |--------------------------------------------------------------------------
    | 1. Find original MaterialEntry
    |--------------------------------------------------------------------------
    */

        $materialEntry = MaterialEntry::where(
            'ministry_id',
            $ministryId
        )
            ->where('project_id', $cboProject)
            ->where('p_name', $p_name)
            ->firstOrFail();

        /*
    |--------------------------------------------------------------------------
    | 2. Original stock
    |--------------------------------------------------------------------------
    */

        $runningBalance = (float) $materialEntry->qty;

        /*
    |--------------------------------------------------------------------------
    | 3. Get all releases
    |--------------------------------------------------------------------------
    */

        $releases = MaterialRelease::where(
            'ministry_id',
            $ministryId
        )
            ->where('project_id', $cboProject)
            ->where('p_name', $p_name)
            ->orderBy('date_release', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 4. Recalculate each release
    |--------------------------------------------------------------------------
    */

        foreach ($releases as $release) {

            $quantityTotal = $runningBalance;

            $quantityRequest = (float) $release->quantity_request;

            $materialTotal = $quantityTotal - $quantityRequest;

            /*
        |--------------------------------------------------------------------------
        | Prevent negative stock
        |--------------------------------------------------------------------------
        */

            if ($materialTotal < 0) {
                throw new \Exception(
                    "ទិន្នន័យមិនគ្រប់ចំនួនសម្រាប់ {$p_name}!"
                );
            }

            /*
        |--------------------------------------------------------------------------
        | Update ledger
        |--------------------------------------------------------------------------
        */

            $release->update([
                'quantity_total' => $quantityTotal,
                // 'quantity_remain' => $materialTotal,
            ]);

            /*
        |--------------------------------------------------------------------------
        | Pass remaining balance to next release
        |--------------------------------------------------------------------------
        */

            $runningBalance = $materialTotal;
        }
    }
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

        $items = MaterialRelease::where('ministry_id', $ministry->id)
            ->where('project_id', $release->project_id)
            ->where('project_sub_id', $release->project_sub_id)
            ->where('date_release', $release->date_release)
            ->get();

        $items->transform(function ($item) use ($ministry, $items) {
            $materialEntry = MaterialEntry::where('p_name', $item->p_name)
                ->where('ministry_id', $ministry->id)
                ->first();

            if ($materialEntry) {
                $otherReleasesTotal = MaterialRelease::where('p_name', $item->p_name)
                    ->where('ministry_id', $ministry->id)
                    ->whereNotIn('id', $items->pluck('id'))
                    ->sum('quantity_total');

                $item->available_stock = max(0, $materialEntry->qty - $otherReleasesTotal);
                $item->is_out_of_stock = ($item->available_stock <= 0);
            } else {
                $item->available_stock = 0;
                $item->is_out_of_stock = true;
            }

            return $item;
        });

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

    // public function update(Request $request, $params, $id)
    // {
    //     $ministryId = decode_params($params);
    //     $realId     = is_numeric($id) ? $id : decode_params($id);

    //     $validated = $request->validate([
    //         'cboProject'    => 'required',
    //         'cboSubProject' => 'nullable',
    //         'date_release'  => 'required|date',
    //         'p_name'        => 'required|array|min:1',
    //         'p_name.*'      => 'required',
    //         'unit'          => 'required|array|min:1',
    //         'unit.*'        => 'required',
    //         'quantity'      => 'required|array|min:1',
    //         'quantity.*'    => 'required|numeric|min:0',
    //         'price'         => 'required|array|min:1',
    //         'price.*'       => 'required|numeric|min:0',
    //         'source'        => 'nullable|array',
    //         'p_year'        => 'nullable|array',
    //     ]);
    //     // dd($validated);
    //     DB::beginTransaction();

    //     try {
    //         $ministry = Ministry::where('id', $ministryId)->firstOrFail();
    //         $project  = Projects::where('id', $validated['cboProject'])->firstOrFail();

    //         $release = MaterialRelease::where('id', $realId)
    //             ->where('ministry_id', $ministry->id)
    //             ->firstOrFail();

    //         $subProjectId = $request->input('cboSubProject') ?: null;
    //         $agencyId     = $request->input('agency', $release->agency_id);

    //         $names      = array_values((array) $request->input('p_name', []));
    //         $units      = array_values((array) $request->input('unit', []));
    //         $quantities = array_values((array) $request->input('quantity', []));
    //         $prices     = array_values((array) $request->input('price', []));
    //         $sources    = array_values((array) $request->input('source', []));
    //         $pYears     = array_values((array) $request->input('p_year', []));

    //         $existingItems = MaterialRelease::where('ministry_id', $ministry->id)
    //             ->where('project_id', $release->project_id)
    //             ->where('project_sub_id', $release->project_sub_id)
    //             ->where('date_release', $release->date_release)
    //             ->get();

    //         // STEP 1: RESTORE ORIGINAL QUANTITIES BEFORE CHECKING NEW QUANTITIES
    //         foreach ($existingItems as $oldItem) {
    //             $oldEntry = MaterialEntry::where('p_name', (string) $oldItem->p_name)->first();
    //             if ($oldEntry) {
    //                 $oldEntry->increment('qty', (float) $oldItem->quantity_total);
    //                 $oldEntry->update([
    //                     'total_price' => (float)$oldEntry->qty * (float)$oldEntry->price
    //                 ]);
    //             }
    //         }

    //         $unitTypes = UnitType::all()->keyBy('id');

    //         // STEP 2: LOOP AND DEDUCT NEW QUANTITIES
    //         foreach ($names as $index => $itemVal) {
    //             $itemStr  = (string) $itemVal;
    //             $unitVal  = $units[$index] ?? null;
    //             $unitName = isset($unitTypes[$unitVal]) ? $unitTypes[$unitVal]->name : $unitVal;
    //             $q        = (float) ($quantities[$index] ?? 0);
    //             $p        = (float) ($prices[$index] ?? 0);

    //             $materialEntry = is_numeric($itemStr)
    //                 ? MaterialEntry::find($itemStr)
    //                 : MaterialEntry::where('p_name', $itemStr)->first();

    //             if ($materialEntry) {
    //                 if ($materialEntry->qty < $q) {
    //                     throw new \Exception("បរិមាណនៅក្នុងស្តុកមិនគ្រប់គ្រាន់ (Insufficient stock for {$materialEntry->p_name})");
    //                 }

    //                 $materialEntry->decrement('qty', $q);
    //                 $materialEntry->update([
    //                     'total_price' => (float)$materialEntry->qty * (float)$materialEntry->price
    //                 ]);

    //                 $pNameValue = $materialEntry->p_name;
    //             } else {
    //                 $pNameValue = $itemStr;
    //             }

    //             $payload = [
    //                 'ministry_id'      => $ministry->id,
    //                 'project_id'       => $project->id,
    //                 'project_sub_id'   => $subProjectId ?? 0,
    //                 'agency_id'        => $agencyId,
    //                 'p_name'           => $pNameValue,
    //                 'p_year'           => $pYears[$index] ?? '',
    //                 'unit'             => $unitName ?? '',
    //                 'quantity_total'   => $release->quantity_total,
    //                 'quantity_request' => $q,
    //                 'price' => $p,
    //                 'total'            => $q * $p,
    //                 'source'           => $sources[$index] ?? null,
    //                 'date_release'     => $request->input('date_release'),
    //             ];

    //             if (isset($existingItems[$index])) {
    //                 $existingItems[$index]->update($payload);
    //             } else {
    //                 MaterialRelease::create($payload);
    //             }
    //         }

    //         // STEP 3: DELETE REMOVED ROWS
    //         if ($existingItems->count() > count($names)) {
    //             for ($i = count($names); $i < $existingItems->count(); $i++) {
    //                 $existingItems[$i]->delete();
    //             }
    //         }

    //         DB::commit();

    //         return redirect()->route('materialRelease.index', $params)
    //             ->with('success', __('រក្សាទុកដោយជោគជ័យ'));
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('MaterialRelease Update Error: ' . $e->getMessage(), [
    //             'id'    => $id,
    //             'trace' => $e->getTraceAsString(),
    //         ]);

    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'បញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage());
    //     }
    // }
    public function update(Request $request, $params, $id)
    {
        $ministryId = decode_params($params);
        $realId     = is_numeric($id) ? $id : decode_params($id);

        $validated = $request->validate([
            'cboProject'    => 'required|integer',
            'cboSubProject' => 'nullable',
            'agency'        => 'required|integer',
            'date_release'  => 'required|date',

            'p_name'        => 'required|array|min:1',
            'p_name.*'      => 'required',

            'unit'          => 'required|array|min:1',
            'unit.*'        => 'required',

            'quantity'      => 'required|array|min:1',
            'quantity.*'    => 'required|numeric|min:0.01',

            'price'         => 'required|array|min:1',
            'price.*'       => 'required|numeric|min:0',

            'source'        => 'nullable|array',
            'p_year'        => 'nullable|array',

            'title'         => 'nullable|array',
            'title.*'       => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | 1. Get Ministry
        |--------------------------------------------------------------------------
        */
            $ministry = Ministry::where('id', $ministryId)
                ->firstOrFail();

            /*
        |--------------------------------------------------------------------------
        | 2. Get Project
        |--------------------------------------------------------------------------
        */
            $project = Projects::where('id', $validated['cboProject'])
                ->firstOrFail();

            /*
        |--------------------------------------------------------------------------
        | 3. Get existing release
        |--------------------------------------------------------------------------
        */
            $release = MaterialRelease::where('id', $realId)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            /*
        |--------------------------------------------------------------------------
        | 4. Get form arrays
        |--------------------------------------------------------------------------
        */
            $names      = array_values($request->input('p_name', []));
            $units      = array_values($request->input('unit', []));
            $quantities = array_values($request->input('quantity', []));
            $prices     = array_values($request->input('price', []));
            $sources    = array_values($request->input('source', []));
            $pYears     = array_values($request->input('p_year', []));
            $titles     = array_values($request->input('title', []));

            /*
        |--------------------------------------------------------------------------
        | 5. Get Unit Types
        |--------------------------------------------------------------------------
        */
            $unitTypes = UnitType::all()->keyBy('id');

            /*
        |--------------------------------------------------------------------------
        | 6. Get ONLY the rows belonging to this release group
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Don't use only project/date because another release may have
        | the same project and date.
        |
        */
            $existingItems = MaterialRelease::where('ministry_id', $ministry->id)
                ->where('project_id', $release->project_id)
                ->where('project_sub_id', $release->project_sub_id)
                ->where('agency_id', $release->agency_id)
                ->where('date_release', $release->date_release)
                ->orderBy('id')
                ->get();

            /*
        |--------------------------------------------------------------------------
        | 7. Restore OLD stock
        |--------------------------------------------------------------------------
        |
        | Use material_entry_id instead of p_name.
        |
        */
            foreach ($existingItems as $oldItem) {

                if ($oldItem->material_entry_id) {

                    $oldEntry = MaterialEntry::lockForUpdate()
                        ->find($oldItem->material_entry_id);

                    if ($oldEntry) {

                        $oldEntry->increment(
                            'qty',
                            (float) $oldItem->quantity_request
                        );

                        $oldEntry->update([
                            'total_price' =>
                            (float) $oldEntry->qty *
                                (float) $oldEntry->price
                        ]);
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | 8. Process NEW rows
        |--------------------------------------------------------------------------
        */
            foreach ($names as $index => $itemVal) {

                $itemStr = (string) $itemVal;

                $unitVal = $units[$index] ?? null;

                $unitName = isset($unitTypes[$unitVal])
                    ? $unitTypes[$unitVal]->name
                    : (string) $unitVal;

                $q = (float) ($quantities[$index] ?? 0);

                $p = (float) ($prices[$index] ?? 0);

                /*
            |--------------------------------------------------------------------------
            | Find MaterialEntry
            |--------------------------------------------------------------------------
            */
                $materialEntry = null;

                if (is_numeric($itemStr)) {

                    $materialEntry = MaterialEntry::lockForUpdate()
                        ->find((int) $itemStr);
                } else {

                    $materialEntry = MaterialEntry::lockForUpdate()
                        ->where('p_name', $itemStr)
                        ->first();
                }

                /*
            |--------------------------------------------------------------------------
            | MaterialEntry is required
            |--------------------------------------------------------------------------
            */
                if (!$materialEntry) {

                    throw new \Exception(
                        "រកមិនឃើញសម្ភារៈក្នុងស្តុក: {$itemStr}"
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | Check stock
            |--------------------------------------------------------------------------
            */
                if ((float) $materialEntry->qty < $q) {

                    throw new \Exception(
                        "បរិមាណនៅក្នុងស្តុកមិនគ្រប់គ្រាន់: {$materialEntry->p_name}"
                            . " (នៅសល់ {$materialEntry->qty}, ត្រូវការ {$q})"
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | Deduct NEW quantity
            |--------------------------------------------------------------------------
            */
                $materialEntry->decrement('qty', $q);

                /*
            |--------------------------------------------------------------------------
            | Recalculate MaterialEntry total
            |--------------------------------------------------------------------------
            */
                $materialEntry->update([
                    'total_price' =>
                    (float) $materialEntry->qty *
                        (float) $materialEntry->price
                ]);

                /*
            |--------------------------------------------------------------------------
            | Values
            |--------------------------------------------------------------------------
            */
                $pNameValue = $materialEntry->p_name;

                $titleValue = $titles[$index]
                    ?? $materialEntry->p_name;

                $projectSubId = $request->input('cboSubProject');

                /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | Schema says project_sub_id is STRING
            |--------------------------------------------------------------------------
            */
                $projectSubId = $projectSubId !== null
                    ? (string) $projectSubId
                    : '';

                /*
            |--------------------------------------------------------------------------
            | Build payload according to migration
            |--------------------------------------------------------------------------
            */
                $payload = [

                    'ministry_id'      => $ministry->id,

                    'project_id'       => $project->id,

                    'project_sub_id'   => $projectSubId,

                    'agency_id'        => $request->input(
                        'agency',
                        $release->agency_id
                    ),

                    /*
                | Required by schema
                */
                    'material_entry_id' => $materialEntry->id,

                    'p_name'            => $pNameValue,

                    'p_year'            => $pYears[$index] ?? '',

                    /*
                | Required by schema
                */
                    'title'             => $titleValue,

                    'unit'              => $unitName ?? '',

                    /*
                | Keep your original total stock field
                */
                    'quantity_total'    => (int) $materialEntry->qty,

                    'quantity_request'  => $q,

                    'price'             => $p,

                    /*
                | Schema column is total_price
                */
                    'total_price'       => $q * $p,

                    'source'            => $sources[$index] ?? null,

                    'date_release'      => $request->input('date_release'),
                ];

                /*
            |--------------------------------------------------------------------------
            | Update existing row / create new row
            |--------------------------------------------------------------------------
            */
                if (isset($existingItems[$index])) {

                    $existingItems[$index]->update($payload);
                } else {

                    MaterialRelease::create($payload);
                }
            }

            /*
        |--------------------------------------------------------------------------
        | 9. Delete removed rows
        |--------------------------------------------------------------------------
        */
            if ($existingItems->count() > count($names)) {

                for (
                    $i = count($names);
                    $i < $existingItems->count();
                    $i++
                ) {

                    $existingItems[$i]->delete();
                }
            }

            /*
        |--------------------------------------------------------------------------
        | 10. Commit
        |--------------------------------------------------------------------------
        */
            DB::commit();

            return redirect()
                ->route('materialRelease.index', $params)
                ->with(
                    'success',
                    __('រក្សាទុកដោយជោគជ័យ')
                );
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'MaterialRelease Update Error: ' . $e->getMessage(),
                [
                    'id'    => $id,
                    'trace' => $e->getTraceAsString(),
                ]
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'បញ្ហាក្នុងការរក្សាទុក: ' .
                        $e->getMessage()
                );
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

            $release = MaterialRelease::where('id', $realId)
                ->where('ministry_id', $ministry->id)
                ->firstOrFail();

            // Dynamic calculation handles remaining stock automatically when release row is deleted
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

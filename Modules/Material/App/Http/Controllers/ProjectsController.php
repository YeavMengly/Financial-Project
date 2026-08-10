<?php

namespace Modules\Material\App\Http\Controllers;

use App\DataTables\Material\InitialProjectsDataTable;
use App\DataTables\Material\ProjectDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Ministry;
use App\Models\Material\Projects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

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

        return $dataTable->render('material::project.index', [
            'params' => $params,
            'ministry' => $ministry,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id   = decode_params($params);
        $ministry = Ministry::where('id', $id)->firstOrFail();

        return view('material::project.create')
            ->with('params', $params)
            ->with('ministry', $ministry);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validated = $request->validate([
            'stock_number'      => 'required|string',
            'stock_name'        => 'required|string',
            'company_name'      => 'required|string',
            'warehouse_voucher' => 'required|string',
            'user_entry'        => 'required|string',
            'user_receiver'     => 'required|string',
            'date'              => 'required|date',
            'title'             => 'nullable|string',
            'file'              => 'nullable|array',
            'file.*'            => 'file|mimes:pdf,doc,docx|max:5120',
            'note'              => 'required|string|max:10000',
            'refer'             => 'required|string|max:10000',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', $id)->firstOrFail();
            $date = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d');

            $paths = [];
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    if ($file->isValid()) {
                        // Fixed: Assigned to $paths[] instead of $stored[]
                        $paths[] = $file->store('materials/documents', 'public');
                    }
                }
            }

            // Create the Material record
            Projects::create([
                'ministry_id'       => $ministry->id,
                'stock_number'      => $validated['stock_number'],
                'stock_name'        => $validated['stock_name'],
                'company_name'      => $validated['company_name'],
                'warehouse_voucher' => $validated['warehouse_voucher'],
                'warehouse_owner'   => 'ក្រសួងការងារ និងបណ្ដុះបណ្ដាលវិជ្ជាជីវៈ',
                'user_entry'        => $validated['user_entry'],
                'user_receiver'     => $validated['user_receiver'],
                'date'              => $date,
                'title'             => $validated['title'] ?? null,
                'note'              => strip_tags($validated['note']),
                'refer'             => strip_tags($validated['refer']),
                'file'              => json_encode($paths),
            ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('project.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

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
    public function edit($params, $id)
    {
        $ministryId = decode_params($params);
        $ministry = Ministry::where('id', $ministryId)->first();
        $module = Projects::where('id', decode_params($id))->where('ministry_id', $ministryId)->firstOrFail();

        return view(
            'material::project.edit',
            [
                'ministry' => $ministry,
                'module' => $module,
                'params' => $params
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validated = $request->validate([
            'stock_number'      => 'required|string',
            'stock_name'        => 'required|string',
            'company_name'      => 'required|string',
            'warehouse_voucher' => 'required|string',
            'user_entry'        => 'required|string',
            'user_receiver'     => 'required|string',
            'date'              => 'required|date',
            'note'              => 'required|string|max:10000',
            'refer'             => 'required|string|max:10000',
        ]);

        DB::beginTransaction();
        try {
            $ministryId = decode_params($params);
            $project = Projects::where('id', $id)->where('ministry_id', $ministryId)->firstOrFail();
            $date = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d');

            // Handle existing files
            $paths = $project->file ? json_decode($project->file, true) : [];

            // If new files are uploaded, you might want to replace or merge them. 
            // Here we assume replacing, but you can change it to append.
            if ($request->hasFile('file')) {
                // Optional: Delete old files from storage
                foreach ($paths as $oldFile) {
                    if (Storage::disk('public')->exists($oldFile)) {
                        Storage::disk('public')->delete($oldFile);
                    }
                }

                $paths = []; // Reset array for new files
                foreach ($request->file('file') as $file) {
                    if ($file->isValid()) {
                        $paths[] = $file->store('materials/documents', 'public');
                    }
                }
            }

            $project->update([
                'stock_number'      => $validated['stock_number'],
                'stock_name'        => $validated['stock_name'],
                'company_name'      => $validated['company_name'],
                'warehouse_voucher' => $validated['warehouse_voucher'],
                'user_entry'        => $validated['user_entry'],
                'user_receiver'     => $validated['user_receiver'],
                'date'              => $date,
                'title'             => $validated['title'] ?? null,
                'note'              => strip_tags($validated['note']),
                'refer'             => strip_tags($validated['refer']),
                'file'              => json_encode($paths),
            ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            return redirect()->route('project.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

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

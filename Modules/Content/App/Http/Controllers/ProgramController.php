<?php

namespace Modules\Content\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialProgramDataTable;
use App\DataTables\Content\ClusterDataTable;
use App\DataTables\Content\ProgramDataTable;
use App\DataTables\Content\ProgramSubDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Cluster;
use App\Models\Content\Ministry;
use App\Models\Content\Program;
use App\Models\Content\ProgramSub;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramController extends Controller
{

    public function getIndex(InitialProgramDataTable $dataTable)
    {
        return $dataTable->render('content::content.program.initialProgram.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ProgramDataTable $dataTable, $params)
    {
        $module = Ministry::where('id', decode_params($params))->first();

        return $dataTable->render('content::content.program.index', [
            'params' => $params,
            'module' => $module
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();

        return view('content::content.program.create')
            ->with('ministry', $ministry)
            ->with('params', $params);
    }

    public function store(Request $request, $params)
    {
        $request->validate([
            'no' => 'required',
            'title' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', decode_params($params))->first();
            Program::create([
                'ministry_id' => $ministry->id,
                'no' => $request->no,
                'title' => strip_tags($request->title),
            ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit == 'save') {
                return redirect()->route('program.index', $params);
            }

            return redirect()->route('program.create', $params);
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('program.index', $params);
        }
    }

    public function edit($params, $id)
    {

        $ministry = Ministry::where('id', decode_params($params))->first();
        $module = Program::where('id', decode_params($id))
            ->where('ministry_id', $ministry->id)
            ->first();

        return view('content::content.program.edit')
            ->with('module', $module)
            ->with('ministry', $ministry)
            ->with('params', $params);
    }

    public function update(Request $request, $params, $id)
    {
        $request->validate([
            'no' => ['required'],
            'title' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $program = Program::findOrfail($id);

            $program->update([
                'no' => $request->no,
                'title' => $request->title,
            ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('program.index',  $params);
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('program.index',  $params);
        }
    }

    public function destroy($params, $id)
    {
        $pid = decode_params($id);

        $ministry = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('id', $pid)
            ->where('ministry_id', $ministry->id)
            ->first();

        $program->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('program.index', $params);
    }

    public function restore($params, $id)
    {
        $pid = decode_params($id);

        Program::withTrashed()->whereKey($pid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('program.index', $params);
    }

    public function subIndex(ProgramSubDataTable $dataTable, $params, $pId)
    {
        $module = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('id', decode_params($pId))
            ->where('ministry_id', $module->id)
            ->first();

        return $dataTable
            ->render('content::content.program.sub.index', [
                'params' => $params,
                'pId' => $pId,
                'module' => $module,
                'program' => $program,
            ]);
    }

    public function subCreate($params, $pId)
    {
        $id = decode_params($pId);

        $ministry = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->first();

        return view('content::content.program.sub.create')
            ->with('params', $params)
            ->with('pId', $pId)
            ->with('ministry', $ministry)
            ->with('program', $program);
    }

    public function subStore(Request $request, $params, $pId)
    {
        $request->validate([
            'no' => ['required'],
            'decription' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $id = decode_params($pId);

            $ministry = Ministry::where('id', decode_params($params))->first();
            $program = Program::where('id', $id)
                ->where('ministry_id', $ministry->id)->first();

            ProgramSub::create([
                'ministry_id' => $ministry->id,
                'program_id' => $program->id,
                'no' => '0' . $request->no,
                'decription' => strip_tags($request->decription),
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit == 'save') {
                return redirect()->route('program.sub.index', [
                    'params' => $params,
                    'pId' => $pId
                ]);
            }

            return redirect()->route('program.sub.create', [
                'params' => $params,
                'pId' => $pId
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route(
                'program.sub.index',
                [
                    'params' => $params,
                    'pId' => $pId
                ]
            );
        }
    }

    public function subEdit($params, $pId, $id)
    {
        $id = decode_params($id);

        $ministry = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('id', decode_params($pId))
            ->where('ministry_id', $ministry->id)->first();
        $module = ProgramSub::where('id', $id)
            ->where('program_id', $program->id)
            ->where('ministry_id', $ministry->id)
            ->first();

        return view('content::content.program.sub.edit')
            ->with('id', $id)
            ->with('pId', $pId)
            ->with('module', $module)
            ->with('ministry', $ministry)
            ->with('params', $params)
            ->with('program', $program);
    }

    public function subUpdate(Request $request, $params, $pId, $id)
    {
        $request->validate([
            'no' => ['required'],
            'decription' => ['required'],
        ]);

        DB::beginTransaction();
        try {

            $ministry = Ministry::where('id', decode_params($params))->first();
            $program = Program::where('id', decode_params($pId))
                ->where('ministry_id', $ministry->id)->first();
            $programSub = ProgramSub::where('id', decode_params($id))
                ->where('program_id', $program->id)
                ->where('ministry_id', $ministry->id)
                ->first();

            $programSub->update([
                'no' => '0' . $request->no,
                'decription' => strip_tags($request->decription)
            ]);

            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('program.sub.index', [
                'params' => $params,
                'pId' => $pId
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('program.sub.index', [
                'params' => $params,
                'pId' => $pId
            ]);
        }
    }

    public function subDestroy($params, $pId, $id)
    {
        $programSub = ProgramSub::where('id', decode_params($id))
            ->where('program_id', decode_params($pId))->first();
        $programSub->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('program.sub.index', [
            'params' => $params,
            'pId' => $pId
        ]);
    }

    public function subRestore($cateId, $id)
    {
        ProgramSub::withTrashed()->find($id)->restore();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('program.sub.index', $cateId);
    }

    public function clusterIndex(ClusterDataTable $dataTable, $params, $pId, $pSubId)
    {
        $module = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('id', decode_params($pId))->first();

        $programSub =  ProgramSub::where('id', decode_params($pSubId))
            ->first();

        return $dataTable->render('content::content.program.sub.cluster.index', [
            'params' => $params,
            'pId' => $pId,
            'pSubId' => $pSubId,
            'module' => $module,
            'program' => $program,
            'programSub' => $programSub
        ]);
    }

    public function clusterCreate($params, $pId, $pSubId)
    {
        $ministry = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('id', decode_params($pId))
            ->where('ministry_id', $ministry->id)->first();
        $programSub = ProgramSub::where('id', decode_params($pSubId))
            ->where('program_id', $program->id)
            ->where('ministry_id', $ministry->id)
            ->first();

        return view('content::content.program.sub.cluster.create')
            ->with('params', $params)
            ->with('pSubId', $pSubId)
            ->with('pId', $pId)
            ->with('ministry', $ministry)
            ->with('program', $program)
            ->with('programSub', $programSub);
    }

    public function clusterStore(Request $request, $params, $pId, $pSubId)
    {
        $request->validate([
            'no' => ['required'],
            'decription' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $ministryId = decode_params($params);
            $programId  = decode_params($pId);
            $programSubId = decode_params($pSubId);

            $ministry = Ministry::where('id', $ministryId)->firstOrFail();
            $programSub = ProgramSub::where('id', $programSubId)->firstOrFail();

            Cluster::create([
                'ministry_id'     => $ministry->id,
                'program_id'      => $programSub->program_id,
                'program_sub_id'  => $programSub->id,
                'no'              => str_pad($request->no, 2, '0', STR_PAD_LEFT),
                'decription'      => strip_tags($request->decription),
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit === 'save') {
                return redirect()->route('cluster.index', [
                    'params' => $params,
                    'pId' => $pId,
                    'pSubId' => $pSubId,
                ]);
            }

            return redirect()->route('cluster.create', [
                'params' => $params,
                'pId' => $pId,
                'pSubId' => $pSubId,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('cluster.create', [
                'params' => $params,
                'pId' => $pId,
                'pSubId' => $pSubId,
            ]);
        }
    }

    public function clusterEdit($params, $pId, $pSubId, $id)
    {

        $id = decode_params($id);

        $ministry = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('id', decode_params($pId))
            ->where('ministry_id', $ministry->id)->first();
        $programSub = ProgramSub::where('id', decode_params($pSubId))
            ->where('program_id', $program->id)
            ->where('ministry_id', $ministry->id)
            ->first();
        $module = Cluster::where('id', $id)
            ->where('ministry_id', $ministry->id)
            ->where('program_id', $program->id)
            ->where('program_sub_id', $programSub->id)
            ->first();

        return view('content::content.program.sub.cluster.edit')
            ->with('id', $id)
            ->with('params', $params)
            ->with('pSubId', $pSubId)
            ->with('pId', $pId)
            ->with('program', $program)
            ->with('programSub', $programSub)
            ->with('ministry', $ministry)
            ->with('module', $module);
    }

    public function clusterUpdate(Request $request, $params, $pId, $pSubId, $id)
    {
        $request->validate([
            'no'         => ['required'],
            'decription' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $ministryId   = decode_params($params);
            $programId    = decode_params($pId);
            $programSubId = decode_params($pSubId);
            $clusterId    = decode_params($id);

            $ministry   = Ministry::where('id', $ministryId)->firstOrFail();
            $programSub = ProgramSub::where('id', $programSubId)->firstOrFail();
            $cluster    = Cluster::where('id', $clusterId)->firstOrFail();

            $cluster->update([
                'ministry_id'     => $ministry->id,
                'program_id'      => $programSub->program_id,
                'program_sub_id'  => $programSub->id,
                'no'              => str_pad($request->no, 2, '0', STR_PAD_LEFT),
                'decription'      => strip_tags($request->decription),
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit === 'save') {
                return redirect()->route('cluster.index', [
                    'params' => $params,
                    'pId'    => $pId,
                    'pSubId' => $pSubId,
                ]);
            }

            return redirect()->route('cluster.index', [
                'params' => $params,
                'pId'    => $pId,
                'pSubId' => $pSubId,
                'id'     => $id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('cluster.index', [
                'params' => $params,
                'pId'    => $pId,
                'pSubId' => $pSubId,
                'id'     => $id,
            ]);
        }
    }

    public function clusterDestroy($params, $pId, $pSubId, $id)
    {
        $cluster = Cluster::where('id', decode_params($id))
            ->where('program_id', decode_params($pId))
            ->where('program_sub_id', decode_params($pSubId))
            ->first();

        $cluster->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('cluster.index', [
            'params' => $params,
            'pId' => $pId,
            'pSubId' => $pSubId
        ]);
    }

    public function  clusterRestore($params, $pId, $pSubId, $id)
    {
        $id = decode_params($id);
        Cluster::withTrashed()->whereKey($id)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route(
            'cluster.index',
            [
                $params,
                $pId,
                $pSubId
            ]
        );
    }
}

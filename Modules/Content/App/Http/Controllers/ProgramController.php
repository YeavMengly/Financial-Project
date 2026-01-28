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
        $id  = decode_params($params);
        $data = Ministry::where('id', $id)->first();

        return $dataTable->render('content::content.program.index', [
            'params' => $params,
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        return view('content::content.program.create')
            ->with('params', $params);
    }

    public function store(Request $request, $params)
    {
        $request->validate([
            'no' => 'required',
            'title' => 'required',
        ]);

        $id = decode_params($params);
        DB::beginTransaction();
        try {
            $ministry = Ministry::where('id', $id)->first();
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
        $id = decode_params($id);
        $module = Program::where('id', $id)->firstOrFail();

        return view('content::content.program.edit')
            ->with('module', $module)
            ->with('params', $params);
    }

    public function destroy($params, $id)
    {
        $pid = decode_params($id);
        $program = Program::where('id', $pid)->first();
        $program->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('program.index', $params);
    }

    public function subIndex(ProgramSubDataTable $dataTable, $params, $pId)
    {
        $data = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('id', decode_params($pId))->first();

        return $dataTable
            ->render('content::content.program.sub.index', [
                'params' => $params,
                'pId' => $pId,
                'data' => $data,
                'program' => $program,
            ]);
    }

    public function subCreate($params, $pId)
    {
        $id = decode_params($pId);

        $module = Program::where('id', $id)->first();

        return view('content::content.program.sub.create')
            ->with('params', $params)
            ->with('pId', $pId)
            ->with('module', $module);
    }

    public function subStore(Request $request, $params, $pId)
    {
        $request->validate([
            'no' => ['required'],
            'decription' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $params = decode_params($params);
            $id = decode_params($pId);

            $program = Program::where('id', $id)->first();
            $ministry = Ministry::where('id', $params)->first();

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
                    'params' => encode_params($ministry->id),
                    'pId' => encode_params($program->id)
                ]);
            }

            return redirect()->route('program.sub.create', [
                'params' => encode_params($ministry->id),
                'pId' => encode_params($program->id)
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

            return redirect()->route('program.sub.index',  ['params' => $params, 'pId' => $program->id]);
        }
    }

    public function subEdit($params, $pId, $id)
    {
        $pId = decode_params($pId);
        $id = decode_params($id);

        $module = Program::where('id', $pId)->first();
        $programSub = ProgramSub::where('id', $id)->first();

        return view('content::content.program.sub.edit')
            ->with('id', $id)
            ->with('pId', $pId)
            ->with('module', $module)
            ->with('params', $params)
            ->with('programSub', $programSub);
    }

    public function subUpdate(Request $request, $params, $pId, $id)
    {
        $request->validate([
            'no' => ['required'],
            'decription' => ['required'],
        ]);

        DB::beginTransaction();
        try {

            $programSub = ProgramSub::where('id', decode_params($id))
                ->where('program_id', decode_params($pId))
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
                'no' => '0' . $request->no,
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

    public function clusterIndex(ClusterDataTable $dataTable, $params, $pId, $pSubId)
    {
        $data = Ministry::where('id', decode_params($params))->first();
        $program = Program::where('id', decode_params($pId))->first();

        $programSub =  ProgramSub::where('id', decode_params($pSubId))
            ->first();

        return $dataTable->render('content::content.program.sub.cluster.index', [
            'params' => $params,
            'pId' => $pId,
            'pSubId' => $pSubId,
            'data' => $data,
            'program' => $program,
            'programSub' => $programSub
        ]);
    }

    public function clusterCreate($params, $pId, $pSubId)
    {
        return view('content::content.program.sub.cluster.create')
            ->with('params', $params)
            ->with('pSubId', $pSubId)
            ->with('pId', $pId);
    }

    public function clusterStore(Request $request, $params, $pId, $pSubId)
    {
        $validated = $request->validate([
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
                'params' => encode_params(decode_params($params)),
                'pId' => encode_params(decode_params($pId)),
                'pSubId' => encode_params(decode_params($pSubId)),
            ]);
        }
    }

    public function clusterEdit($params, $pId, $pSubId, $id)
    {
        $module = Cluster::where('ministry_id', decode_params($params))
            ->where('program_id', decode_params($pId))
            ->where('program_sub_id', decode_params($pSubId))
            ->findOrFail($id);

        return view('content::content.program.sub.cluster.edit')
            ->with('params', $params)
            ->with('pSubId', $pSubId)
            ->with('pId', $pId)
            ->with('module', $module);
    }

    public function clusterUpdate($params, $pId, $pSubId, $id)
    {
        $cluster = Cluster::findOrFail($id);

        return view('content::content.program.sub.cluster.edit')
            ->with('params', $params)
            ->with('pSubId', $pSubId)
            ->with('pId', $pId)
            ->with('cluster', $cluster);
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

<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AnnualOpen\InitialProgramDataTable;
use App\DataTables\ProgramDataTable;
use App\DataTables\ProgramSubDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Ministry;
use App\Models\Program;
use App\Models\ProgramSub;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramController extends Controller
{

    public function getIndex(InitialProgramDataTable $dataTable)
    {
        return $dataTable->render('beginningcredit::initialProgram.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ProgramDataTable $dataTable, $params)
    {
        $id  = decode_params($params);
        $data = Ministry::where('id', $id)->first();

        return $dataTable->render('beginningcredit::program.index', [
            'params' => $params,
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        return view('beginningcredit::program.create')
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

        return view('beginningcredit::program.edit')
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
        $id  = decode_params($params);
        $pId  = decode_params($pId);

        $data = Ministry::where('id', $id)->first();
        $pId = Program::where('id', $pId)->first();

        $programSub =  ProgramSub::where('ministry_id', $id)
            ->whereIn('program_id', $pId)
            ->get();

        return $dataTable
            ->render('beginningcredit::program.sub.index', [
                'pId' => encode_params($pId->id),
                'module' => $pId,
                'data' => $data,
                'params' => $params,
                'programSub' => $programSub
            ]);
    }

    public function subCreate($params, $pId)
    {
        $id = decode_params($pId);

        $module = Program::where('id', $id)->first();

        return view('beginningcredit::program.sub.create')
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

        return view('beginningcredit::program.sub.edit')
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
                'no' => $request->no,
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
        // $params = decode_params($params);
        $pid = decode_params($id);

        Program::withTrashed()->whereKey($pid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('program.index', $params);
    }
}

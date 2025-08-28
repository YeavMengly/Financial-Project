<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\ProgramSubDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Ministry;
use App\Models\Program;
use App\Models\ProgramSub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramSubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProgramSubDataTable $dataTable, $params,)
    {
        $id = decode_params($params);
        $data = Ministry::where('id', $id)->first();

        // dd($data);
        return $dataTable->render(
            'beginningcredit::programs.programSub.index',
            [
                'data' => $data,
                'params' => $params,
                // 'pid' => $pid,
            ]
        );
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $data = Program::where('ministry_id', $id)->get();

        return view('beginningcredit::programs.programSub.create')
            ->with('data', $data)
            ->with('params', $params);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $request->validate([
            'cboNo' => ['required'],
            'no' => ['required'],
            'decription' =>  ['required'],
        ]);

        $id = decode_params($params);

        DB::beginTransaction();

        try {
            $existingRecord = ProgramSub::where('program_id', $request->cboNo)
                ->where('no', $request->no)
                ->first();

            if ($existingRecord) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(__('messages.sub.depart'), 'បញ្ហា')
                    ->flash();

                return redirect()->back()->withInput();
            }

            $ministries = Ministry::where('id', $id)->first();
            ProgramSub::create([
                'ministry_id' => $ministries->id,
                'program_id' => $request->cboNo,
                'no' => '0' . $request->no,
                'decription' => strip_tags($request->decription)
            ]);
            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit == 'save') {
                return redirect()->route('programSub.index', $params);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('programSub.index', $params);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('beginningcredit::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params)
    {
        $id = decode_params($params);
        $ministry  = Ministry::where('id', $id)->first();
        $program = Program::where('ministry_id', $ministry->id)->first();
        $data = ProgramSub::where('program_id', $program->id)->first();

        return view('beginningcredit::programs.programSub.edit')
            ->with('data', $data)
            ->with('params', $params)
            ->with('program', $program);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $request->validate([
            'cboNo' => ['required'],
            'no' => ['required'],
            'decription' =>  ['required'],
        ]);

        $id = decode_params($params);
        $programSub = ProgramSub::where('id', $id)->first();

        DB::beginTransaction();
        try {
            $existingRecord = ProgramSub::where('program_id', $request->cboNo)
                ->where('no', $request->no)
                ->where('id', '!=', $id) // Exclude the current record
                ->first();

            if ($existingRecord) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(__('messages.sub.program'), 'បញ្ហា') // Use a defined translation or custom message
                    ->flash();

                return redirect()->back()->withInput();
            }

            $programSub->update([
                'number' => $request->no,
                'decription' => strip_tags($request->decription)
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit == 'save') {
                return redirect()->route('programSub.index',  encode_params($programSub->ministry_id));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('programSub.index', encode_params($programSub->ministry_id));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $programSub = ProgramSub::where('id', $id)->first();
        $programSub->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('programSub.index', encode_params($programSub->ministry_id));
    }
}

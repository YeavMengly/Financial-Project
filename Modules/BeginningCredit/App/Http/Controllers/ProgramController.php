<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\ProgramDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Ministry;
use App\Models\Program;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramController extends Controller
{
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

    /**
     * Store a newly created resource in storage.
     */
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

            DB::commit(); // Commit the transaction

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('program.index', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('program.index', $params);
        }
    }
    /**
     */
    public function edit($params)
    {
        $id = decode_params($params);
        $data = Program::where('id', $id)->first();

        return view('beginningcredit::program.edit')
            ->with('data', $data)
            ->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $request->validate([
            'no' => 'required',
            'title' => 'required',
        ]);

        $id  = decode_params($params);
        $program = Program::where('id', $id)->first();

        $program->update([
            'no' => $request->no,
            'title' =>  strip_tags($request->title),
        ]);

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('success_msg', 'successful')
            ->flash();

        return redirect()->route('program.index', encode_params($program->ministry_id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $program = Program::where('id', $id)->first();
        $program->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('program.index', encode_params($program->ministry_id));
    }
}

<?php

namespace Modules\Content\App\Http\Controllers;

use App\DataTables\Content\AgencyDataTable;
use App\DataTables\AnnualOpen\InitialAgencyDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Agency;
use App\Models\Content\Ministry;
use App\Models\Program;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgencyController extends Controller
{

    public function getIndex(InitialAgencyDataTable $dataTable)
    {
        return $dataTable->render('content::content.agency.initialAgency.index');
    }

    /**
     * Display a listing of the resource.zz
     */
    public function index(AgencyDataTable $dataTable, $params)
    {
        $id  = decode_params($params);
        $data = Ministry::where('id', $id)->first();

        return $dataTable->render('content::content.agency.index', [
            'data' => $data,
            'params' => $params
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $data = Program::where('ministry_id', $id)->get();


        // dd($data);

        return view('content::content.agency.create')->with('params', $params)
            ->with('data', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $validateData = $request->validate([
            'cboProgram' => ['required'],
            'no' => ['required'],
            'name' => ['required'],
            'nick_name' => ['required'],
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {

            $ministries = Ministry::where('id', $id)->first();
            Agency::create([
                'ministry_id' => $ministries->id,
                'program_id' => $validateData['cboProgram'],
                'no' => $validateData['no'],
                'name' => $validateData['name'],
                'nick_name' => $validateData['nick_name'],

            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('agency.index', $params);
        } catch (Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('agency.index', $params);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $id = decode_params($id);
        $program = Program::where('ministry_id', decode_params($params))->get();
        $agency = Agency::where('id', $id)->first();

        return view('beginningcredit::agency.edit')->with('params', $params)->with('agency', $agency)->with('program', $program);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $validateData = $request->validate([
            'cboProgram' => ['required'],
            'no' => ['required'],
            'name' => ['required'],
            'nick_name' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $agency = Agency::findOrfail($id);
            $agency->update([
                'program_id' => $validateData['cboProgram'],
                'no' => $validateData['no'],
                'name' => $validateData['name'],
                'nick_name' => $validateData['nick_name'],
            ]);
            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('agency.index', $params);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('agency.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $id = decode_params($id);
        $agency = Agency::where('id', $id)->first();
        $agency->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('agency.index', $params);
    }

    public function restore($params, $id)
    {
        $pid = decode_params($id);

        Agency::withTrashed()->whereKey($pid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('agency.index', $params);
    }
}

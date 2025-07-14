<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AgencyDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Agency;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AgencyDataTable $dataTable)
    {
        $agency = Agency::all();
        return $dataTable->render('beginningcredit::agency.index', ['agency' => $agency]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('beginningcredit::agency.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validateData = $request->validate([
            'agencyNumber' => ['required'],
            'agencyTitle' => ['required'],
        ]);

        DB::beginTransaction();

        try {

            Agency::create([
                'agencyNumber' => $validateData['agencyNumber'],
                'agencyTitle' => $validateData['agencyTitle'],
            ]);

            DB::commit(); // Commit the transaction

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('agency.index');
        } catch (Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('agency.index');
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
        $data = Agency::where('id', $id)->first();

        return view('beginningcredit::agency.edit')->with('params', $params)->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $validateData = $request->validate([
            'agencyNumber' => ['required'],
            'agencyTitle' => ['required'],
        ]);

        $id = decode_params($params);

        DB::beginTransaction();

        try {
            $agency = Agency::where('id', $id)->first();


            $agency->update([
                'agencyNumber' => $validateData['agencyNumber'],
                'agencyTitle' => $validateData['agencyTitle'],
            ]);

            DB::commit(); // Commit the transaction

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('agency.index');
        } catch (Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('agency.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        //
        $id = decode_params($params);
        $agency = Agency::where('id', $id)->first();
        $agency->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('agency.index');
    }
}

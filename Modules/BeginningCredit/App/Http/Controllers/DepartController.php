<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\DepartDataTable;
use App\Http\Controllers\Controller;
use App\Models\Depart;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(DepartDataTable $dataTable)
    {
        $depart = Depart::all();
        return $dataTable->render('beginningcredit::depart.index', ['depart' => $depart]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('beginningcredit::depart.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'depart' => ['required'],
            'txtDepart' => ['required'],
        ]);

        DB::beginTransaction();

        try {

            Depart::create([
                'depart' => $validateData['depart'],
                'txtDepart' => strip_tags($validateData['txtDepart']),
            ]);

            DB::commit(); // Commit the transaction

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('depart.index');
        } catch (Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('depart.index');
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
        $data = Depart::where('id', $id)->first();

        return view('beginningcredit::depart.edit')->with('data', $data)->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $validateData = $request->validate([
            'depart' => ['required'],
            'txtDepart' => ['required'],
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {

            $depart = Depart::where('id', $id)->first();
            $depart->update([
                'depart' => $validateData['depart'],
                'txtDepart' => strip_tags($validateData['txtDepart']),
            ]);

            DB::commit(); 
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('depart.index');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('depart.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $depart = Depart::where('id', $id)->first();
        $depart->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();
        return redirect()->route('depart.index');
    }
}

<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\SubDepartDataTable;
use App\Http\Controllers\Controller;
use App\Models\Depart;
use App\Models\SubDepart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubDepartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SubDepartDataTable $dataTable)
    {
        $subDepart = SubDepart::all();

        return $dataTable->render('beginningcredit::depart.subDepart.index', ['subDepart' => $subDepart]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = Depart::all();

        return view('beginningcredit::depart.subDepart.create')->with('data', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cboSubDepart' => ['required'],
            'subDepart' => ['required'],
            'txtSubDepart' =>  ['required'],
        ]);

        DB::beginTransaction();
        try {
            $existingRecord = SubDepart::where('depart_id', $request->cboSubDepart)
                ->where('subDepart', $request->subDepart)
                ->first();

            if ($existingRecord) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(__('messages.sub.depart'), 'បញ្ហា') // Use a defined translation or custom message
                    ->flash();

                return redirect()->back()->withInput();
            }

            SubDepart::create([
                'depart_id' => $request->cboSubDepart,
                'subDepart' => $request->subDepart,
                'txtSubDepart' => strip_tags($request->txtSubDepart)
            ]);
            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit == 'save') {
                return redirect()->route('subDepart.index');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('subDepart.index');
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
        $data = Depart::all();
        $subDepart = SubDepart::where('id', $id)->first();

        return view('beginningcredit::depart.subDepart.edit')->with('data', $data)->with('subDepart', $subDepart)->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $request->validate([
            'cboSubDepart' => ['required'],
            'subDepart' => ['required'],
            'txtSubDepart' =>  ['required'],
        ]);
        $id = decode_params($params);
        DB::beginTransaction();
        try {
            $existingRecord = SubDepart::where('depart_id', $request->cboSubDepart)
                ->where('subDepart', $request->subDepart)
                ->where('id', '!=', $id) // Exclude the current record
                ->first();

            if ($existingRecord) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(__('messages.sub.depart'), 'បញ្ហា') // Use a defined translation or custom message
                    ->flash();

                return redirect()->back()->withInput();
            }
            $subDepart = SubDepart::where('id', $id)->first();
            $subDepart->update([
                'depart_id' => $request->cboSubDepart,
                'subDepart' => $request->subDepart,
                'txtSubDepart' => strip_tags($request->txtSubDepart)
            ]);
            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit == 'save') {
                return redirect()->route('subDepart.index');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('subDepart.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $subDepart = SubDepart::where('id', $id)->first();
        $subDepart->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('subDepart.index');
    }
}

<?php

namespace Modules\Setting\App\Http\Controllers;

use App\DataTables\ApiKeyDataTable;
use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ApiKeyDataTable $dataTable)
    {
        return $dataTable->render('setting::keys.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'txtTitle' => ['required'],
            'txtKey' => ['required'],
        ]);
        DB::beginTransaction();
        try {
            ApiKey::create([
                'title' => $request->txtTitle,
                'key' => $request->txtKey,
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            if ($request->submit == 'save') {
                return redirect()->route('keys.index');
            }

            return redirect()->route('keys.create');
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('keys.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('setting::keys.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $keys = ApiKey::where('id', $id)->first();

        return view('setting::keys.edit')->with('keys', $keys);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'txtTitle' => ['required'],
            'txtKey' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $keys = ApiKey::where('id', $id)->first();
            $keys->update([
                'title' => $request->txtTitle,
                'key' => $request->txtKey,
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('keys.index');
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('keys.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $keys = ApiKey::where('id', $id)->first();
        $keys->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('keys.index');
    }

    public function restore($id)
    {
        ApiKey::withTrashed()->find($id)->restore();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('keys.index');
    }
}

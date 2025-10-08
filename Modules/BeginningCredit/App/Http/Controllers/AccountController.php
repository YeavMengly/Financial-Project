<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AccountDataTable;
use App\DataTables\AnnualOpen\InitialAccountDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Account;
use App\Models\BeginCredit\Ministry;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function getIndex(InitialAccountDataTable $dataTable)
    {
        return $dataTable->render('beginningcredit::accounts.initialAccount.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(AccountDataTable $dataTable, $params)
    {
        $id  = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $account =  Account::where('ministry_id', $id)->get();

        return $dataTable->render(
            'beginningcredit::accounts.index',
            [
                'data' => $data,
                'params' => $params,
                'account' => $account
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params)
    {
        $id = decode_params($params);
        $chapter = Chapter::where('ministry_id', $id)->get();

        return view('beginningcredit::accounts.create')
            ->with('params', $params)
            ->with('chapter', $chapter);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params)
    {
        $request->validate([
            'cboChapterNumber' => ['required'],
            'no' => ['required'],
            'name' => ['required'],
        ]);

        $id = decode_params($params);
        DB::beginTransaction();
        try {
            $existingRecord = Account::where('chapter_id', $request->cboChapterNumber)
                ->where('no', $request->no)
                ->first();

            if ($existingRecord) {
                return redirect()->back()->withErrors([
                    'account.number' => __('messages.account.number')
                ])->withInput();
            }

            $ministry = Ministry::where('id', $id)->first();

            Account::create([
                'ministry_id' => $ministry->id,
                'chapter_id' => $request->cboChapterNumber,
                'no' => $request->no,
                'name' => $request->name
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit == 'save') {
                return redirect()->route('accounts.index', $params);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('accounts.index', $params);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $id)
    {
        $id = decode_params($id);
        $chapter = Chapter::all();
        $module = Account::where('id', $id)->first();

        return view('beginningcredit::accounts.edit')->with('module', $module)->with('chapter', $chapter)->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $id)
    {
        $request->validate([
            'cboChapterNumber' => ['required'],
            'no' => ['required'],
            'name' => ['required'],
        ]);

        DB::beginTransaction();
        $account = Account::where('id', $id)->first();
        try {
            $existingRecord = Account::where('chapter_id', $request->cboChapterNumber)
                ->where('no', $request->no)
                ->where('id', '<>', $id)
                ->first();

            if ($existingRecord) {
                return redirect()->back()->withErrors([
                    'account.number' => __('messages.account.number')
                ])->withInput();
            }
            $account->update([
                'chapter_id' => $request->cboChapterNumber,
                'no' => $request->no,
                'name' => $request->name
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('accounts.index', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('accounts.index', $params);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $id)
    {
        $id = decode_params($id);
        $account = Account::where('id', $id)->first();
        $account->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('accounts.index', encode_params($account->ministry_id));
    }

     /**
     * Restore the specified resource from storage.
     */
    public function restore($params, $id)
    {
        $aid = decode_params($id);

        Account::withTrashed()->whereKey($aid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('accounts.index', $params);
    }
}

<?php

namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AccountDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Account;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AccountDataTable $dataTable)
    {
        $account =  Account::all();
        return $dataTable->render('beginningcredit::accounts.index', ['account' => $account]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $chapter = Chapter::all();
        return view('beginningcredit::accounts.create')->with('chapter', $chapter);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cboChapterNumber' => ['required'],
            'accountNumber' => ['required'],
            'txtAccount' => ['required'],
        ]);

        DB::beginTransaction();
        try {
            $existingRecord = Account::where('chapterNumber', $request->cboChapterNumber)
                ->where('accountNumber', $request->accountNumber)
                ->first();

            if ($existingRecord) {
                return redirect()->back()->withErrors([
                    'account.number' => __('messages.account.number')
                ])->withInput();
            }

            Account::create([
                'chapterNumber' => $request->cboChapterNumber,
                'accountNumber' => $request->accountNumber,
                'txtAccount' => $request->txtAccount
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit == 'save') {
                return redirect()->route('account.index');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('account.index');
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
        $chapter = Chapter::all();
        $account = Account::where('id', $id)->first();

        return view('beginningcredit::accounts.edit')->with('account', $account)->with('chapter', $chapter)->with('params', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params)
    {
        $request->validate([
            'cboChapterNumber' => ['required'],
            'accountNumber' => ['required'],
            'txtAccount' => ['required'],
        ]);

        $id = decode_params($params);
        DB::beginTransaction();
        try {
            $existingRecord = Account::where('chapterNumber', $request->cboChapterNumber)
                ->where('accountNumber', $request->accountNumber)
                ->where('id', '<>', $id)
                ->first();

            if ($existingRecord) {
                return redirect()->back()->withErrors([
                    'account.number' => __('messages.account.number')
                ])->withInput();
            }
            $account = Account::where('id', $id)->first();

            $account->update([
                'chapterNumber' => $request->cboChapterNumber,
                'accountNumber' => $request->accountNumber,
                'txtAccount' => $request->txtAccount
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            // if ($request->submit == 'save') {
            return redirect()->route('account.index');
            // }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('account.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params)
    {
        $id = decode_params($params);
        $account = Account::where('id', $id)->first();
        $account->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('account.index');
    }
}

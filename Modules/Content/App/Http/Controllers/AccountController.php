<?php

namespace Modules\Content\App\Http\Controllers;

use App\DataTables\Content\AccountDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Account;
use App\Models\Content\Ministry;
use App\Models\Content\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AccountDataTable $dataTable, $params, $chId)
    {
        $id  = decode_params($params);
        $module = Ministry::where('id', $id)->first();
        $chapter =  Chapter::where('id', decode_params($chId))
            ->where('ministry_id', decode_params($params))->first();
        $account =  Account::where('ministry_id', $id)->get();

        return $dataTable->render(
            'content::content.accounts.index',
            [
                'module' => $module,
                'params' => $params,
                'account' => $account,
                'chId' => $chId,
                'chapter' => $chapter
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params, $chId)
    {
        $id = decode_params($chId);
        $module = Ministry::where('id', decode_params($params))->first();
        $chapter = Chapter::where('id', $id)->first();

        return view('content::content.accounts.create')
            ->with('params', $params)
            ->with('chId', $chId)
            ->with('module', $module)
            ->with('chapter', $chapter);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params, $chId)
    {
        $request->validate([
            'no' => ['required'],
            'name' => ['required'],
        ]);

        DB::beginTransaction();
        try {

            $id = decode_params($params);

            $ministry = Ministry::where('id', $id)->first();
            $chapter = Chapter::where('id', decode_params($chId))->first();

            $existingRecord = Account::where('chapter_id', $chapter->id)
                ->where('no', $request->no)
                ->first();

            if ($existingRecord) {
                return redirect()->back()->withErrors([
                    'account.number' => __('messages.account.number')
                ])->withInput();
            }

            Account::create([
                'ministry_id' => $ministry->id,
                'chapter_id' => $chapter->id,
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
                return redirect()->route(
                    'accounts.index',
                    [
                        'params' => $params,
                        'chId' => $chId
                    ]
                );
            }

            return redirect()->route('accounts.create', [
                'params' => $params,
                'chId' => $chId
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

            return redirect()->route('accounts.index',  ['params' => $params, 'chId' => encode_params($chId)]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $chId, $id)
    {
        $id = decode_params($id);
        $module = Ministry::where('id', decode_params($params))->first();
        $chapter = Chapter::where('id', decode_params($chId))->first();
        $account = Account::where('id', $id)
            ->where('chapter_id', $chapter->id)
            ->first();

        return view('content::content.accounts.edit')
            ->with('module', $module)
            ->with('account', $account)
            ->with('chapter', $chapter)
            ->with('params', $params)
            ->with('chId', $chId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $chId, $id)
    {


        $request->validate([
            'no' => ['required'],
            'name' => ['required'],
        ]);

        DB::beginTransaction();

        try {

            $ministry = Ministry::where('id', decode_params($params))->first();
            $chapter = Chapter::where('id', decode_params($chId))->first();

            $account = Account::where('id', $id)
                ->where('chapter_id', $chapter->id)
                ->where('ministry_id', $ministry->id)
                ->first();


            if (!$account) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(__('messages.account.not.found'), 'បញ្ហា')
                    ->flash();

                return redirect()->route('accounts.index', ['params' => $params, 'chId' => $chId]);
            }
            $account->update([
                'no' => $request->no,
                'name' => $request->name
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('accounts.index', ['params' => $params, 'chId' => $chId]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('accounts.index', ['params' => $params, 'chId' => $chId]);
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

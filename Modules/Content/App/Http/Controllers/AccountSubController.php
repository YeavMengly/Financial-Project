<?php


namespace Modules\Content\App\Http\Controllers;

use App\DataTables\Content\AccountSubDataTable;
use App\Http\Controllers\Controller;
use App\Models\Content\Account;
use App\Models\Content\AccountSub;
use App\Models\Content\Chapter;
use App\Models\Content\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountSubController extends Controller
{

    /**
     * Display a listing of the resource
     */
    public function index(AccountSubDataTable $dataTable, $params, $chId, $accId)
    {

        // dd($params, $chId, $accId);

        $id  = decode_params($params);
        $module = Ministry::where('id', $id)->first();
        $chapter =  Chapter::where('id', decode_params($chId))
            ->where('ministry_id', decode_params($params))->first();

        $account =  Account::where('id', decode_params($accId))
            ->where('ministry_id', $id)->first();

        $accountSub =  AccountSub::where('chapter_id', decode_params($chId))
            ->where('account_id', decode_params($accId))
            ->where('ministry_id', $id)->get();

        return $dataTable->render('content::content.accounts.accountSub.index', [
            'module' => $module,
            'params' => $params,
            'chId' => $chId,
            'accId' => $accId,
            'chapter' => $chapter,
            'account' => $account,
            'accountSub' => $accountSub,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($params, $chId, $accId)
    {
        $id  = decode_params($params);
        $module = Ministry::where('id', $id)->first();
        $chapter =  Chapter::where('id', decode_params($chId))
            ->where('ministry_id', decode_params($params))->first();
        $account =  Account::where('id', decode_params($accId))
            ->where('ministry_id', $id)->first();
        $accountSub =  AccountSub::where('chapter_id', decode_params($chId))
            ->where('account_id', decode_params($accId))
            ->where('ministry_id', $id)->first();

        return view('content::content.accounts.accountSub.create')
            ->with('module', $module)
            ->with('chapter', $chapter)
            ->with('account', $account)
            ->with('accountSub', $accountSub)
            ->with('params', $params)
            ->with('chId', $chId)
            ->with('accId', $accId);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $params, $chId, $accId)
    {
        $request->validate([
            'no' => ['required'],
            'name' =>  ['required'],
        ]);

        // dd($request->all(), $params, $chId, $accId);
        DB::beginTransaction();
        try {
            $id = decode_params($params);

            $ministry = Ministry::where('id', $id)->first();
            $chapter = Chapter::where('id', decode_params($chId))->first();
            $account = Account::where('id', decode_params($accId))
                ->where('chapter_id', $chapter->id)->first();


            $existingRecord = AccountSub::where('account_id', $account->id)
                ->where('no', $request->no)
                ->first();

            if ($existingRecord) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(__('messages.sub.account.number'), 'បញ្ហា') // Use a defined translation or custom message
                    ->flash();

                return redirect()->back()->withInput();
            }

            $ministry = Ministry::where('id', $id)->first();

            AccountSub::create([
                'ministry_id' => $ministry->id,
                'chapter_id' => $chapter->id,
                'account_id' => $account->id,
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
                return redirect()->route('accountSub.index', ['params' => $params, 'chId' => $chId, 'accId' => $accId]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('accountSub.index', ['params' => $params, 'chId' => $chId, 'accId' => $accId]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($params, $chId, $accId, $id)
    {
        $id = decode_params($id);
        $module = Ministry::where('id', decode_params($params))->first();
        $chapter = Chapter::where('id', decode_params($chId))
            ->where('ministry_id', decode_params($params))->first();
        $account = Account::where('id', decode_params($accId))
            ->where('chapter_id', $chapter->id)->first();
        $accountSub = AccountSub::where('id', $id)
            ->where('chapter_id', decode_params($chId))
            ->where('account_id', decode_params($accId))
            ->where('ministry_id', decode_params($params))->first();

        return view('content::content.accounts.accountSub.edit')
            ->with('module', $module)
            ->with('chapter', $chapter)
            ->with('account', $account)
            ->with('accountSub', $accountSub)
            ->with('params', $params)
            ->with('chId', $chId)
            ->with('accId', $accId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $params, $chId, $accId, $id)
    {
        $request->validate([
            'no' => ['required'],
            'name' => ['required'],
        ]);

        DB::beginTransaction();
        try {

            $id = decode_params($id);
            $chapter = Chapter::where('id', decode_params($chId))->first();
            $account = Account::where('id', decode_params($accId))
                ->where('chapter_id', $chapter->id)->first();

            $existingRecord = AccountSub::where('account_id', $account->id)
                ->where('chapter_id', $chapter->id)
                ->where('no', $request->no)
                ->where('id', '<>', $id)
                ->first();

            if (!$existingRecord) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(__('messages.sub.account.number'), 'បញ្ហា')
                    ->flash();

                return redirect()->back()->withInput();
            }

            $existingRecord->update([
                'no' => $request->no,
                'name' => $request->name,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('accountSub.index', ['params' => $params, 'chId' => $chId, 'accId' => $accId]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('accountSub.index', ['params' => $params, 'chId' => $chId, 'accId' => $accId]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($params, $chId, $accId, $id)
    {
        $id = decode_params($id);

        $accountSub = AccountSub::where('id', $id)
            ->where('chapter_id', decode_params($chId))
            ->where('account_id', decode_params($accId))
            ->first();

        $accountSub->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('accountSub.index', [
            'params' => $params,
            'chId' => $chId,
            'accId' => $accId
        ]);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($params, $chId, $accId, $id)
    {
        $asid = decode_params($id);

        AccountSub::withTrashed()->whereKey($asid)->restore();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('accountSub.index', ['params' => $params, 'chId' => $chId, 'accId' => $accId]);
    }
}

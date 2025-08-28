<?php


namespace Modules\BeginningCredit\App\Http\Controllers;

use App\DataTables\AccountSubDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Account;
use App\Models\BeginCredit\AccountSub;
use App\Models\BeginCredit\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountSubController extends Controller
{

    public function index(AccountSubDataTable $dataTable, $params)
    {
        $id  = decode_params($params);
        $data = Ministry::where('id', $id)->first();
        $accountSub =  AccountSub::where('ministry_id', $id)->get();

        return $dataTable->render('beginningcredit::accounts.accountSub.index', [
            'data' => $data,
            'params' => $params,
            'accountSub' => $accountSub,
        ]);
    }

    public function create($params)
    {
        $id  = decode_params($params);
        $data = Account::where('ministry_id', $id)->get();

        return view('beginningcredit::accounts.accountSub.create')
        ->with('account', $data)
        ->with('params', $params);
    }

    public function store(Request $request, $params)
    {
        $request->validate([
            'cboAccountNumber' => ['required'],
            'no' => ['required'],
            'name' =>  ['required'],
        ]);

        $id = decode_params($params);
        DB::beginTransaction();
        try {
            $existingRecord = AccountSub::where('account_id', $request->cboAccountNumber)
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
                'account_id' => $request->cboAccountNumber,
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
                return redirect()->route('accountSub.index', $params);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('accountSub.index', $params);
        }
    }

    public function edit($params)
    {
        $id = decode_params($params);
        $account = Account::all();
        $accountSub = AccountSub::where('id', $id)->first();

        return view('beginningcredit::accounts.accountSub.edit')->with('accountSub', $accountSub)->with('account', $account)->with('params', $params);
    }

    public function update(Request $request, $params)
    {
        $request->validate([
            'cboAccountNumber' => ['required'],
            'no' => ['required'],
            'name' => ['required'],
        ]);

        $id = decode_params($params);
        DB::beginTransaction();
        $accountSub = AccountSub::where('id', $id)->first();

        try {
            $existingRecord = AccountSub::where('account_id', $request->cboAccountNumber)
                ->where('no', $request->no)
                ->where('id', '<>', $id)
                ->first();

            if ($existingRecord) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(__('messages.sub.account.number'), 'បញ្ហា')
                    ->flash();

                return redirect()->back()->withInput();
            }

            $accountSub->update([
                'account_id' => $request->cboAccountNumber,
                'no' => $request->no,
                'name' => $request->name,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('accountSub.index', encode_params($accountSub->ministry_id));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('accountSub.index', encode_params($accountSub->ministry_id));
        }
    }

    public function destroy($params)
    {
        $id = decode_params($params);
        $accountSub = AccountSub::where('id', $id)->first();
        $accountSub->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('accountSub.index', encode_params($accountSub->ministry_id));
    }
}

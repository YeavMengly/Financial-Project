<?php


namespace Modules\BeginningCredit\App\Http\Controllers;


use App\DataTables\SubAccountDataTable;
use App\Http\Controllers\Controller;
use App\Models\BeginCredit\Account;
use App\Models\BeginCredit\SubAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubAccountController extends Controller
{

    public function index(SubAccountDataTable $dataTable)
    {
        $subAccount = SubAccount::all();
        return $dataTable->render('beginningcredit::accounts.subAccount.index', ['subAccount' => $subAccount]);
    }

    public function create()
    {
        $account =  Account::all();

        return view('beginningcredit::accounts.subAccount.create')->with('account', $account);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cboAccountNumber' => ['required'],
            'subAccountNumber' => ['required'],
            'txtSubAccount' =>  ['required'],
        ]);

        DB::beginTransaction();
        try {
            $existingRecord = SubAccount::where('accountNumber', $request->accountNumber)
                ->where('subAccountNumber', $request->subAccountNumber)
                ->first();

            if ($existingRecord) {
                flash()
                    ->translate('en')
                    ->option('timeout', 2000)
                    ->error(__('messages.sub.account.number'), 'បញ្ហា') // Use a defined translation or custom message
                    ->flash();

                return redirect()->back()->withInput();
            }

            SubAccount::create([
                'accountNumber' => $request->cboAccountNumber,
                'subAccountNumber' => $request->subAccountNumber,
                'txtSubAccount' => $request->txtSubAccount
            ]);
            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            if ($request->submit == 'save') {
                return redirect()->route('subAccount.index');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('subAccount.index');
        }
    }

    public function edit($params)
    {

        $id = decode_params($params);
        $account = Account::all();
        $subAccount = SubAccount::where('id', $id)->first();

        return view('beginningcredit::accounts.subAccount.edit')->with('subAccount', $subAccount)->with('account', $account)->with('params', $params);
    }

    public function update(Request $request, $params)
    {
        $request->validate([
            'cboAccountNumber' => ['required'],
            'subAccountNumber' => ['required'],
            'txtSubAccount' => ['required'],
        ]);

        $id = decode_params($params);
        DB::beginTransaction();

        try {
            $existingRecord = SubAccount::where('accountNumber', $request->cboAccountNumber)
                ->where('subAccountNumber', $request->subAccountNumber)
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
            $subAccount = SubAccount::where('id', $id)->first();

            $subAccount->update([
                'accountNumber' => $request->cboAccountNumber,
                'subAccountNumber' => $request->subAccountNumber,
                'txtSubAccount' => $request->txtSubAccount,
            ]);

            DB::commit();

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('subAccount.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($e->getMessage(), 'បញ្ហា')
                ->flash();

            return redirect()->route('subAccount.index');
        }
    }

    public function destroy($params)
    {
        $id = decode_params($params);
        $subAccount = SubAccount::where('id', $id)->first();
        $subAccount->delete();

        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('subAccount.index');
    }
}

<?php

namespace Modules\Setting\App\Http\Controllers;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{

    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('setting::user.index');
    }

    public function create() {
        $role = Role::where("id", "!=", 1)->get();
        return view('setting::user.create')->with('role', $role);
    }

    public function store(Request $request) {
        $request->validate([
            'txtUsername' => ['required', 'min:4', 'unique:users,username'],
            'txtPassword' => ['required', 'min:6'],
            'cboRole' => ['required'],
            'txtName' => ['required']
        ]);
        DB::beginTransaction();
        try {
            $role = Role::findOrfail($request->cboRole);
            User::create([
                'fullname'  => $request->txtName,
                'role_id'  => $request->cboRole,
                'username' => $request->txtUsername,
                'email' => $request->txtUsername.'@gmail.com',
                'password' => bcrypt($request->txtPassword),
                'permissions' => $role->permissions
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('user.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('kh')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();
            return redirect()->route('user.index');
        }
    }

    public function edit($id) {
        $user = User::findOrfail($id);
        $role = Role::where("id", "!=", 1)->get();
        return view('setting::user.edit')->with('role', $role)->with('user', $user);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'cboRole' => ['required'],
            'txtName' => ['required']
        ]);
        DB::beginTransaction();
        try {
            $user = User::findOrfail($id);
            $role = Role::findOrfail($request->cboRole);
            $user->update([
                'fullname'  => $request->txtName,
                'role_id'  => $request->cboRole,
                'permissions' => $role->permissions
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('user.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('kh')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();
            return redirect()->route('user.index');
        }
    }

    public function destroy($id) {
        $user = User::findOrfail($id);
        $user->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();
        return redirect()->route('user.index');
    }

    public function restore($id) {
        User::withTrashed()->find($id)->restore();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();
        return redirect()->route('user.index');
    }

    public function password($id) {
        $user = User::findOrfail($id);
        return view('setting::user.password')->with('user', $user);
    }

    public function passwordChange(Request $request, $id) {
        $request->validate([
            'password' => ['required', 'min:6', 'confirmed']
        ]);
        DB::beginTransaction();
        try {
            $user = User::findOrfail($id);
            $user->update([
                'password'  => bcrypt($request->password)
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('user.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('kh')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();
            return redirect()->route('user.index');
        }
    }

}

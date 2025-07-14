<?php

namespace Modules\Profile\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{

    public function index()
    {
        $role = Role::findOrFail(auth()->user()->role_id);
        return view('profile::index')
            ->with('role', $role);
    }

    public function password() {
        $role = Role::findOrFail(auth()->user()->role_id);
        return view('profile::password')
            ->with('role', $role);
    }

    public function passwordChange(Request $request) {
        $request->validate([
            'password' => ['required', 'min:6', 'confirmed']
        ]);
        DB::beginTransaction();
        try {
            $user = User::findOrfail(auth()->user()->id);
            $user->update([
                'password'  => bcrypt($request->password)
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('profile.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('kh')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();
            return redirect()->route('profile.index');
        }
    }
}

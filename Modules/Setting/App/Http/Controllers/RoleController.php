<?php

namespace Modules\Setting\App\Http\Controllers;

use App\DataTables\RoleDataTable;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    protected $role;

    protected $permission;

    public function __construct(RoleRepository $role, PermissionRepository $permission)
    {
        $this->role = $role;
        $this->permission = $permission;
    }

    public function index(RoleDataTable $dataTable)
    {
        return $dataTable->render('setting::role.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'txtRole' => ['required'],
        ]);
        DB::beginTransaction();
        try {
            Role::create([
                'name' => $request->txtRole,
                'permissions' => arrayCheck('permissions', $request->all()) ? $request->permissions : [],
            ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();
            if ($request->submit == 'save') {
                return redirect()->route('role.index');
            }

            return redirect()->route('role.create');
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('role.index');
        }
    }

    public function create()
    {
        $pemissions = $this->permission->all();

        return view('setting::role.create')->with('pemissions', $pemissions);
    }

    public function edit($id)
    {
        try {
            $role = $this->role->edit($id);
            $pemissions = $this->permission->all();

            return view('setting::role.edit')
                ->with('role', $role)
                ->with('pemissions', $pemissions);
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('role.index');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'txtRole' => ['required'],
        ]);
        DB::beginTransaction();
        try {
            $role = Role::where('id', $id)->first();
            $role->update([
                'name' => $request->txtRole,
                'permissions' => arrayCheck('permissions', $request->all()) ? $request->permissions : [],
            ]);
            User::where('role_id', $id)
                ->update([
                    'permissions' => arrayCheck('permissions', $request->all()) ? $request->permissions : [],
                ]);
            DB::commit();
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->success('success_msg', 'successful')
                ->flash();

            return redirect()->route('role.index');
        } catch (Exception $e) {
            DB::rollBack();
            $bug = $e->getMessage();
            Log::error($bug);
            flash()
                ->translate('en')
                ->option('timeout', 2000)
                ->error($bug, 'បញ្ហា')
                ->flash();

            return redirect()->route('role.index');
        }
    }

    public function destroy($id)
    {
        $role = Role::where('id', $id)->first();
        $role->delete();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->error('delete_msg', 'delete')
            ->flash();

        return redirect()->route('role.index');
    }

    public function restore($id)
    {
        $role = Role::withTrashed()->where('id', $id)->first();
        $role->restore();
        flash()
            ->translate('en')
            ->option('timeout', 2000)
            ->success('restore_msg', 'restore')
            ->flash();

        return redirect()->route('role.index');
    }
}

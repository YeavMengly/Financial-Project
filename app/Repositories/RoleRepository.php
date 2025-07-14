<?php
namespace App\Repositories;

use App\Models\Role;

class RoleRepository {
    public function all()
    {
        return Role::all();
    }

    public function edit($id)
    {
        return Role::findOrfail($id);
    }
}

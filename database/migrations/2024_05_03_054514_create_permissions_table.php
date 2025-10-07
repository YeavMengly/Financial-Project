<?php

use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 151);
            $table->string('attribute')->nullable();
            $table->mediumtext('keywords')->nullable();
            $table->timestamps();
        });

        $attributes = [
            'roles'                  => [
                'view'   => 'role.index',
                'create' => 'role.create',
                'edit'   => 'role.edit',
                'delete' => 'role.destroy',
            ],
            'ministries'                  => [
                'view'   => 'ministries.index',
                'create' => 'ministries.create',
                'edit'   => 'ministries.edit',
                'delete' => 'ministries.destroy',
            ],

            'begin.vouchers'                  => [
                'view'   => 'beginVoucher.index',
                'create' => 'beginVoucher.create',
                'edit'   => 'beginVoucher.edit',
                'delete' => 'beginVoucher.destroy',
            ],

            'categories'                  => [
                'view'   => 'category.index',
                'create' => 'category.create',
                'edit'   => 'category.edit',
                'delete' => 'category.destroy',
            ],

            'chapters'                  => [
                'show'   => 'initialChapter.index',
                'view'   => 'chapters.index',
                'create' => 'chapters.create',
                'edit'   => 'chapters.edit',
                'delete' => 'chapters.destroy',
            ],

            'accounts'                  => [
                'show'   => 'initialAccount.index',
                'view'   => 'accounts.index',
                'create' => 'accounts.create',
                'edit'   => 'accounts.edit',
                'delete' => 'accounts.destroy',
            ],

            'sub.account'                  => [
                'show'   => 'initialAccountSub.index',
                'view'   => 'accountSub.index',
                'create' => 'accountSub.create',
                'edit'   => 'accountSub.edit',
                'delete' => 'accountSub.destroy',
            ],

            'program'                  => [
                'show'   => 'initialProgram.index',
                'view'   => 'program.index',
                'create' => 'program.create',
                'edit'   => 'program.edit',
                'delete' => 'program.destroy',
            ],

            'program.sub'                  => [
                'show'   => 'initialProgramSub.index',
                'view'   => 'programSub.index',
                'create' => 'programSub.create',
                'edit'   => 'programSub.edit',
                'delete' => 'programSub.destroy',
            ],

            'agency'                  => [
                'show'   => 'initialAgency.index',
                'view'   => 'agency.index',
                'create' => 'agency.create',
                'edit'   => 'agency.edit',
                'delete' => 'agency.destroy',
            ],




        ];

        $admin_permission = [];
        Permission::whereNotNull('id')->delete();

        foreach ($attributes as $key => $attribute) {
            $permission            = new Permission;
            $permission->name      = str_replace('_', ' ', $key);
            $permission->attribute = $key;
            $permission->keywords  = $attribute;
            $permission->save();
            foreach ($attribute as $index => $permit) {
                $admin_permission[] = trim($permit);
            }
            $user                  = User::first();
            $user->permissions     = $admin_permission;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};

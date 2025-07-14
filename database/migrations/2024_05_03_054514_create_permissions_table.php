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
                'view'   => 'roles.index',
                'create' => 'roles.create',
                'edit'   => 'roles.edit',
                'delete' => 'roles.destroy',
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

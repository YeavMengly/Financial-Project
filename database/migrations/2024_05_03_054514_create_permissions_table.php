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

            /**
             *   Content
             */

            'content.ministries'                  => [
                'view'   => 'ministries.index',
                'create' => 'ministries.create',
                'edit'   => 'ministries.edit',
                'delete' => 'ministries.destroy',
            ],

            // 'categories'                  => [
            //     'view'   => 'category.index',
            //     'create' => 'category.create',
            //     'edit'   => 'category.edit',
            //     'delete' => 'category.destroy',
            // ],

            'content.chapters'                  => [
                'show'   => 'initialChapter.index',
                'view'   => 'chapters.index',
                'create' => 'chapters.create',
                'edit'   => 'chapters.edit',
                'delete' => 'chapters.destroy',
            ],

            'content.accounts'                  => [
                'show'   => 'initialAccount.index',
                'view'   => 'accounts.index',
                'create' => 'accounts.create',
                'edit'   => 'accounts.edit',
                'delete' => 'accounts.destroy',
            ],

            'content.sub.accounts'                  => [
                'show'   => 'initialAccountSub.index',
                'view'   => 'accountSub.index',
                'create' => 'accountSub.create',
                'edit'   => 'accountSub.edit',
                'delete' => 'accountSub.destroy',
            ],

            'content.program'                  => [
                'show'   => 'initialProgram.index',
                'view'   => 'program.index',
                'create' => 'program.create',
                'edit'   => 'program.edit',
                'delete' => 'program.destroy',
            ],

            'content.program.sub'                  => [
                'view'   => 'programSub.index',
                'create' => 'programSub.create',
                'edit'   => 'programSub.edit',
                'delete' => 'programSub.destroy',
            ],

            'content.cluster'                  => [
                'view'   => 'programSub.index',
                'create' => 'programSub.create',
                'edit'   => 'programSub.edit',
                'delete' => 'programSub.destroy',
            ],

            'content.agency'                  => [
                'show'   => 'initialAgency.index',
                'view'   => 'agency.index',
                'create' => 'agency.create',
                'edit'   => 'agency.edit',
                'delete' => 'agency.destroy',
            ],

            'content.expense.type'                  => [
                'view'   => 'expenseType.index',
                'create' => 'expenseType.create',
                'edit'   => 'expenseType.edit',
                'delete' => 'expenseType.destroy',
            ],

            /**
             *   Begin Credit
             */

            'begin.vouchers'                  => [
                'show'   => 'initialBudgetVoucher.index',
                'view'   => 'beginVoucher.index',
                'create' => 'beginVoucher.create',
                'edit'   => 'beginVoucher.edit',
                'delete' => 'beginVoucher.destroy',
            ],

            // 'begin.mandates'                  => [
            //     'show'   => 'initialBudgetMandate.index',
            //     'view'   => 'beginMandate.index',
            //     'create' => 'beginMandate.create',
            //     'edit'   => 'beginMandate.edit',
            //     'delete' => 'beginMandate.destroy',
            // ],

            /**
             *   Payment
             */

            'payment'                  => [
                'show'   => 'initialVoucher.index',
                'view'   => 'budgetVoucher.index',
                'create' => 'budgetVoucher.create',
                'edit'   => 'budgetVoucher.edit',
                'delete' => 'budgetVoucher.destroy',
            ],

            'advance.payment'                  => [
                'show'   => 'initialAdvancePayment.index',
                'view'   => 'budgetAdvancePayment.index',
                'create' => 'budgetAdvancePayment.create',
                'edit'   => 'budgetAdvancePayment.edit',
                'delete' => 'budgetAdvancePayment.destroy',
            ],

            'expenditure.guarantee'                  => [
                'show'   => 'initialMandate.index',
                'view'   => 'budgetMandate.index',
                'create' => 'budgetMandate.create',
                'edit'   => 'budgetMandate.edit',
                'delete' => 'budgetMandate.destroy',
            ],

            // Water
            'water'                  => [
                'show'   => 'initialWater.index',
                'view'   => 'water.index',
                'create' => 'water.create',
                'edit'   => 'water.edit',
                'delete' => 'water.destroy',
            ],

            // Electric
            'electric'                  => [
                'show'   => 'initialElectric.index',
                'view'   => 'electric.index',
                'create' => 'electric.create',
                'edit'   => 'electric.edit',
                'delete' => 'electric.destroy',
            ],

            // Duel
            'duel.entry'                  => [
                'show'   => 'initialDuelEntry.index',
                'view'   => 'duelEntry.index',
                'create' => 'duelEntry.create',
                'edit'   => 'duelEntry.edit',
                'delete' => 'duelEntry.destroy',
            ],

            'duel.release'                  => [
                'show'   => 'initialDuelRelease.index',
                'view'   => 'duelRelease.index',
                'create' => 'duelRelease.create',
                'edit'   => 'duelRelease.edit',
                'delete' => 'duelRelease.destroy',
            ],

            // Material
            'material.entry'                  => [
                'show'   => 'initialMaterialEntry.index',
                'view'   => 'materialEntry.index',
                'create' => 'materialEntry.create',
                'edit'   => 'materialEntry.edit',
                'delete' => 'materialEntry.destroy',
            ],
            'material.release'                  => [
                'show'   => 'initialMaterialRelease.index',
                'view'   => 'materialRelease.index',
                'create' => 'materialRelease.create',
                'edit'   => 'materialRelease.edit',
                'delete' => 'materialRelease.destroy',
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

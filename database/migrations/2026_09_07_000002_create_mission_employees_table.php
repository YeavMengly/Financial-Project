<?php

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
        Schema::create('mission_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->unsignedBigInteger('mission_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('position_id');
            $table->string('level_name', 5);
            // Allowances
            $table->decimal('travel_allowance', 15, 0)->default(0);
            $table->decimal('pocket_money', 15, 0)->default(0);
            $table->decimal('total_pocket_money', 15, 0)->default(0);
            $table->decimal('meal_money', 15, 0)->default(0);
            $table->decimal('total_meal_money', 15, 0)->default(0);
            $table->decimal('accommodation_money', 15, 0)->default(0);
            $table->decimal('total_accommodation_money', 15, 0)->default(0);
            $table->decimal('total', 15, 0)->default(0);
            // Assign budget checkbox
            $table->boolean('assign_budget')
                ->default(false);
            $table->string('account_number', 15)->nullable();
            $table->unsignedBigInteger('payment_account_employee_id', 5)->nullable();
            $table->string('payment_account_number', 15)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_employees');
    }
};

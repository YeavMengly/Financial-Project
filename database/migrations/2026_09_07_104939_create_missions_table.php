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
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('level_id');

            $table->string('legal_number', 10);
            $table->date('legal_date');

            $table->text('description');

            $table->unsignedBigInteger('province_id');

            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedTinyInteger('days_count')->default(0);
            $table->unsignedTinyInteger('nights_count')->default(0);

            // Money
            $table->decimal('travel_allowance', 15, 0)->default(0);

            $table->decimal('pocket_money', 15, 0)->default(0);
            $table->decimal('total_pocket_money', 15, 0)->default(0);

            $table->decimal('meal_money', 15, 0)->default(0);
            $table->decimal('total_meal_money', 15, 0)->default(0);

            $table->decimal('accommodation_money', 15, 0)->default(0);
            $table->decimal('total_accommodation_money', 15, 0)->default(0);

            $table->decimal('total', 15, 0)->default(0);

            $table->enum('mission_type', ['local', 'abroad']);

            $table->boolean('mission_type_is_archived')->default(true);

            $table->boolean('assign_budget')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};

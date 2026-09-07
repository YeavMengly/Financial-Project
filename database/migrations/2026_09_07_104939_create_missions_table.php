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
            $table->unsignedBigInteger('name_list_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('level_id');
            $table->string('legal_number');
            $table->date('legal_date');
            $table->text('description');
            $table->unsignedBigInteger('province_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count');
            $table->integer('nights_count');
            $table->decimal('travel_allowance');
            $table->decimal('pocket_money');
            $table->decimal('total_pocket_money');
            $table->decimal('meal_money');
            $table->decimal('totak_meal_money');
            $table->decimal('accommodation_money');
            $table->decimal('total_accommodation_money');
            $table->enum('mission_type', ['local', 'abroad']);// 1=local, 2=abroad
            $table->integer('mission_type_is_archived');
            $table->decimal('total');
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

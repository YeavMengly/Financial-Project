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
        Schema::create('electric_entities', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('ministry_id');
            $table->string('title_entity');
            $table->string('location_number');
            $table->unsignedBigInteger('province_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_entities');
    }
};

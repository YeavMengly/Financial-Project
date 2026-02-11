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
        Schema::create('electrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->string('title_entity');
            $table->string('location_number_use')->nullable();
            $table->date('date')->nullable();
            $table->date('use_start')->nullable();
            $table->date('use_end')->nullable();
            $table->decimal('kilo', 10, 2)->default(0);
            $table->string('reactive_energy')->nullable();
            $table->decimal('cost_total', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electrics');
    }
};

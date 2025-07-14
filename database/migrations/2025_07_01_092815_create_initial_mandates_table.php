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
        Schema::create('initial_mandates', function (Blueprint $table) {
            $table->id();
            $table->string('year')->unique(); // Stores the year as a date (e.g., 'YYYY-01-01')
            $table->string('title');
            $table->string('sub_title');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initial_mandates');
    }
};

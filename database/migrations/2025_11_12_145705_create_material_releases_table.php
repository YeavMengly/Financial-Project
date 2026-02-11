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
        Schema::create('material_releases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->string('p_code');
            $table->string('p_name');
            $table->string('p_year');
            $table->string('title');
            $table->string('unit');
            $table->integer('quantity_total');
            $table->integer('quantity_request');
            $table->decimal('total', 15, 0);
            $table->string('source')->nullable();
            $table->text('refer')->nullable();
            $table->date('date_release');
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_releases');
    }
};

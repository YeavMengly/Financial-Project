<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('public_service_allocation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('public_service_agency_id');
            $table->string('code', 10);
            $table->string('title', 10);
            $table->integer('price');
            $table->integer('parent_id', 4)->default(0);
            $table->
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_service_allocation');
    }
};

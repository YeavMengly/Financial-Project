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
        Schema::create('public_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_code');
            $table->string('service_prefix_name')->nullable();
            $table->string('service_name');
            $table->unsignedBigInteger('categories_id');
            $table->integer('service_price');
            $table->tinyInteger('max_service_time')->default(0);
            $table->tinyInteger('expired')->default(0);
            $table->decimal('percentage_national', 10, 2)->default(0);
            $table->decimal('percentage_mlvt', 10, 2)->default(0);
            $table->decimal('percentage_interior', 10, 2)->default(0);
            $table->decimal('percentage_mef', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_services');
    }
};

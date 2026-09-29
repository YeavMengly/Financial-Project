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
        Schema::create('material_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->unsignedBigInteger('project_id');
            $table->string('project_sub_id')->nullable();
            $table->string('program_id')->nullable();
            $table->string('program_sub_id')->nullable();
            $table->string('cluster_id')->nullable();
            $table->string('account_sub_id')->nullable();
            $table->string('p_name');
            $table->string('p_year');
            $table->string('unit');
            $table->integer('qty');
            $table->decimal('price', 15, 0);
            $table->decimal('total_price', 15, 0);
            $table->string('source')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_entries');
    }
};

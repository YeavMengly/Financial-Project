<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agency_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('program_sub_id')->nullable()->constrained('program_subs')->cascadeOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained('clusters')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['agency_id', 'program_id', 'program_sub_id', 'cluster_id'], 'agency_hierarchy_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agency_assignments');
    }
};
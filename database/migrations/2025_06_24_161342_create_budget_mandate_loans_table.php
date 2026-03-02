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
        Schema::create('budget_mandate_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('program_sub_id');
            $table->unsignedBigInteger('cluster_id');
            $table->unsignedBigInteger('account_sub_id');
            $table->string('no');
            $table->decimal('internal_increase', 15, 2)->default(0);
            $table->decimal('unexpected_increase', 15, 2)->default(0);
            $table->decimal('additional_increase', 15, 2)->default(0);
            $table->decimal('total_increase', 15, 2)->default(0);
            $table->decimal('decrease', 15, 2)->default(0);
            $table->decimal('editorial', 15, 2)->default(0);
            $table->text('txtDescription');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_mandate_loans');
    }
};

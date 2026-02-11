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
        Schema::create('begin_mandates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('program_sub_id');
            $table->unsignedBigInteger('cluster_id');
            $table->unsignedBigInteger('chapter_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('account_sub_id');
            $table->string('no');
            $table->text('txtDescription');
            $table->decimal('fin_law', 15, 0)->default(0);
            $table->decimal('current_loan', 15, 0)->default(0);
            $table->decimal('new_credit_status', 15, 0)->default(0);
            $table->decimal('early_balance', 15, 0)->default(0);
            $table->decimal('apply', 15, 0)->default(0);
            $table->decimal('deadline_balance', 15, 0)->default(0);
            $table->decimal('credit', 15, 0)->default(0);
            $table->decimal('law_average', 15, 2)->default(0);
            $table->decimal('law_correction', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('begin_credit_mandates');
    }
};

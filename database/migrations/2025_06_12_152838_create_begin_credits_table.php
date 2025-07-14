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
        Schema::create('begin_credits', function (Blueprint $table) {
            $table->id();

            // Foreign key to sub_accounts

            $table->unsignedBigInteger('agencyNumber');
            $table->unsignedBigInteger('subDepart');
            $table->unsignedBigInteger('subAccountNumber');
            // Other columns
            $table->string('program');
            $table->text('txtDescription');
            $table->decimal('fin_law', 15, 0)->default(0);
            $table->decimal('current_loan', 15, 0)->default(0);

            // Foreign key to years
            $table->unsignedBigInteger('year');

            $table->decimal('new_credit_status', 15, 0)->default(0);
            $table->decimal('early_balance', 15, 0)->default(0);
            $table->decimal('apply', 15, 0)->default(0);
            $table->decimal('deadline_balance', 15, 0)->default(0);
            $table->decimal('credit', 15, 0)->default(0);
            $table->decimal('law_average', 15, 0)->default(0);
            $table->decimal('law_correction', 15, 0)->default(0);

            // Composite unique constraint
            // $table->unique(['subAccountNumber', 'program']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('begin_credits');
    }
};

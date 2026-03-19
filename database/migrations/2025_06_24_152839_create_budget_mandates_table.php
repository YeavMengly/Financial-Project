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
        Schema::create('budget_mandates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('program_sub_id');
            $table->unsignedBigInteger('cluster_id');
            $table->unsignedBigInteger('account_sub_id');
            $table->unsignedBigInteger('no');
            $table->decimal('budget', 15, 2)->default(0);
            $table->unsignedBigInteger('expense_type_id');
            $table->string('legal_id');
            $table->string('payment_voucher_number');
            $table->string('legal_number', 100);
            $table->string('legal_name');
            $table->enum('status', ['todo', 'done'])->default('todo');
            $table->integer('is_archived')->default(1);
            $table->text('description');
            $table->json('attachments')->nullable();
            $table->date('transaction_date');
            $table->date('request_date');
            $table->date('legal_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_mandates');
    }
};

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
        Schema::create('budget_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->unsignedBigInteger('agency_id');
            $table->unsignedBigInteger('account_sub_id');
            $table->unsignedBigInteger('no');
            $table->decimal('budget', 15, 2)->default(0);
            $table->string('task_type');
            $table->string('legalNumber');
            $table->text('txtDescription');
            $table->json('attachments')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_vouchers');
    }
};

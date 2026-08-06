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
        Schema::create('budget_allocations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ministry_id');
            
            $table->foreignId('budget_begin_voucher_id')
                ->constrained('begin_vouchers')
                ->cascadeOnDelete();

            $table->foreignId('budget_expense_type_id')
                ->constrained('expense_types')
                ->cascadeOnDelete();

            $table->decimal('amount', 18, 2)->default(0);

            $table->timestamps();

            $table->unique([
                'budget_begin_voucher_id',
                'budget_expense_type_id'
            ]);

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_allocations');
    }
};

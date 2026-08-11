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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->string('stock_number');
            $table->string('stock_name');
            $table->string('company_name');
            $table->string('warehouse_voucher');
            $table->string('warehouse_owner');
            $table->string('user_entry');
            $table->string('user_receiver');
            $table->date('date');
            $table->string('title')->nullable();
            $table->text('note')->nullable();
            $table->text('refer')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

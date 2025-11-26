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
        Schema::create('duel_releases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ministry_id');
            $table->string('item_name');
            $table->string('receipt_number');
            $table->string('stock_number');
            $table->string('agency_name');
            $table->string('user_request');
            $table->string('unit');
            $table->integer('quantity_total');
            $table->integer('quantity_request');
            $table->decimal('duel_total', 15, 0);
            $table->text('note')->nullable();
            $table->text('refer')->nullable();
            $table->date('date_release');
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duel_releases');
    }
};

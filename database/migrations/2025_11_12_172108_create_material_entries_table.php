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
            $table->string('company_name');
            $table->string('stock_number');
            $table->string('stock_name');
            $table->string('user_entry');
            $table->string('p_code');
            $table->string('p_name');
            $table->string('p_year');
            $table->string('title');
            $table->string('unit');
            $table->integer('quantity');
            $table->decimal('price', 15, 0);
            $table->decimal('total_price', 15, 0);
            $table->string('source')->nullable();
            $table->text('note')->nullable();
            $table->text('refer')->nullable();
            $table->date('date_entry');
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
        Schema::dropIfExists('material_entries');
    }
};

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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
<<<<<<< HEAD
            $table->decimal('pocket_money', 10, 0);
            $table->decimal('meal_money', 10, 0);
            $table->decimal('accommodation_money', 10, 0);
=======
            $table->decimal('pocket_money');
            $table->decimal('meal_money');
            $table->decimal('accommodation_money');
>>>>>>> 7e99b755e862642799051072924caf2210ea68d0
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};

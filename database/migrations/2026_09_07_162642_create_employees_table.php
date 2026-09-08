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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('id_number')->nullable()->unique();
<<<<<<< HEAD:database/migrations/2026_09_07_094017_create_name_list_table.php
            $table->string('account_number', 15)->unique();
            $table->string('name_kh', 50)->unique();
            $table->string('name_latin', 50)->unique();
=======
            $table->string('account_number')->unique();
            $table->string('name_kh')->unique();
            $table->string('name_latin')->unique();
>>>>>>> 7e99b755e862642799051072924caf2210ea68d0:database/migrations/2026_09_07_162642_create_employees_table.php
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

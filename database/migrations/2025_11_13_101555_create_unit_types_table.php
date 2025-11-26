<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('unit_types')->insert([
            [
                'name' => 'គ្រឿង',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'លីត្រ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'រាយ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ដុំ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ឈុត',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_types');
    }
};

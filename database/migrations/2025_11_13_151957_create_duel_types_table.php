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
        Schema::create('duel_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_km');    // Khmer
            $table->string('name_latn');  // Latin/English
            $table->timestamps();
        });

        DB::table('duel_types')->insert([
            [
                'name_km' => 'ប្រេងសាំង',
                'name_latn' => 'Gasoline',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_km' => 'ប្រេងម៉ាស៊ូត',
                'name_latn' => 'Diesel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_km' => 'ប្រេងម៉ាស៊ីន',
                'name_latn' => 'Engine Oil',
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
        Schema::dropIfExists('duel_types');
    }
};

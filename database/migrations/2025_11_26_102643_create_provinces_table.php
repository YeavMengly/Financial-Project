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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        /*
        |-------------------------------------------------------------------------------
        | Insert data into province_cities table
        |-------------------------------------------------------------------------------
        */
        DB::table('provinces')->insert([
            ['name' => 'ភ្នំពេញ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កណ្ដាល', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពង់ចាម', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កែប', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'បន្ទាយមានជ័យ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពង់ឆ្នាំង', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពង់ស្ពឺ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពង់ធំ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពត', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កោះកុង', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ក្រចេះ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'មណ្ឌលគីរី', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ឧត្តរមានជ័យ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ប៉ៃលិន', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ព្រះសីហនុ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ព្រះវិហារ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ពោធិ៍សាត់', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'រតនគីរី', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'សៀមរាប', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ស្ទឹងត្រែង', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ស្វាយរៀង', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'តាកែវ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ត្បូងឃ្មុំ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ព្រៃវែង', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'បាត់ដំបង', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};

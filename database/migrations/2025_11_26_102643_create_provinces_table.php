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
            $table->decimal('budget', 10, 0)->default(0);
            $table->timestamps();
        });

        /*
        |------------------0..-------------------------------------------------------------
        | Insert data into province_cities table
        |-------------------------------------------------------------------------------
        */
        DB::table('provinces')->insert([
            ['name' => 'ភ្នំពេញ', 'budget' => 0,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កណ្ដាល', 'budget' => 17600, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពង់ចាម', 'budget' => 201600, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កែប', 'budget' => 256000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'បន្ទាយមានជ័យ', 'budget' => 576000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពង់ឆ្នាំង', 'budget' => 145600, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពង់ស្ពឺ', 'budget' => 72000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពង់ធំ', 'budget' => 268800, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កំពត', 'budget' => 235200, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'កោះកុង', 'budget' => 464000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ក្រចេះ', 'budget' => 537600, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'មណ្ឌលគីរី', 'budget' => 611200, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ឧត្តរមានជ័យ', 'budget' => 705600, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ប៉ៃលិន', 'budget' => 630400, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ព្រះសីហនុ', 'budget' => 361600, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ព្រះវិហារ', 'budget' => 480000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ពោធិ៍សាត់', 'budget' => 296000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'រតនគីរី', 'budget' => 942400, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'សៀមរាប', 'budget' => 499200, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ស្ទឹងត្រែង', 'budget' => 748800, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ស្វាយរៀង', 'budget' => 200000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'តាកែវ', 'budget' => 123200, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ត្បូងឃ្មុំ', 'budget' => 273600, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ព្រៃវែង', 'budget' => 144000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'បាត់ដំបង', 'budget' => 465600, 'created_at' => now(), 'updated_at' => now()],
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

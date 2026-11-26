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
        Schema::create('public_service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->text('service_cate_name');
            $table->integer('service_is_ministry')->default(0);
            $table->integer('parent')->default(0);
            $table->timestamps();
        });

        DB::table('public_service_categories')->insert([
            // Service Category Data for ministry
            [
                'code' => '001',
                'service_cate_name' => 'ផ្នែកសុវត្ថិភាពសនិងសុខភាពការងារ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '002',
                'service_cate_name' => 'ផ្នែកអធិការកិច្ចការងារ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '003',
                'service_cate_name' => 'ផ្នែកវិវាទការងារ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '004',
                'service_cate_name' => 'ផ្នែកការងារកុមារ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [

                'code' => '005',
                'service_cate_name' => 'ផ្នែកវហ្វឹកហ្វឺនកូនជាង',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [

                'code' => '006',
                'service_cate_name' => 'ផ្នែកអប់រំបណ្ដុះបណ្ដាលបច្ចេកទេសនិងវិជ្ជាជីវៈ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '007',
                'service_cate_name' => 'ផ្នែកមុខរបរ និងហត្ថពលកម្ម',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '008',
                'service_cate_name' => 'ផ្នែកកម្មករនិយោជិតជាជនបរទេស',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '009',
                'service_cate_name' => 'ផ្នែកសេវារកការងារឱ្យធ្វើ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],

            // Service Category Data for province
            [
                'code' => '010',
                'service_cate_name' => 'ផ្នែកអធិការកិច្ចការងារ',
                'service_is_ministry' => 0,
                'parent' => 0
            ],
            [
                'code' => '011',
                'service_cate_name' => 'ផ្នែកវិវាទការងារ',
                'service_is_ministry' => 0,
                'parent' => 0
            ],
            [
                'code' => '012',
                'service_cate_name' => 'ផ្នែកការងារកុមារ',
                'service_is_ministry' => 0,
                'parent' => 0
            ],
            [
                'code' => '013',
                'service_cate_name' => 'ផ្នែកហ្វឹកហ្វឺនកូនជាង',
                'service_is_ministry' => 0,
                'parent' => 0
            ],

            [
                'code' => '014',
                'service_cate_name' => 'ផ្នែកអប់រំបណ្ដុះបណ្ដាលបច្ចេកទេសនិងវិជ្ជាជីវៈ',
                'service_is_ministry' => 0,
                'parent' => 0
            ],

            [
                'code' => '015',
                'service_cate_name' => 'ផ្នែកមុខរបរ និងហត្ថពលកម្ម',
                'service_is_ministry' => 0,
                'parent' => 0
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_service_categories');
    }
};

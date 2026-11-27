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
                'code' => '1',
                'service_cate_name' => 'ផ្នែកសុវត្ថិភាពនិងសុខភាពការងារ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '2',
                'service_cate_name' => 'ផ្នែកអធិការកិច្ចការងារ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '3',
                'service_cate_name' => 'ផ្នែកវិវាទការងារ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '4',
                'service_cate_name' => 'ផ្នែកការងារកុមារ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [

                'code' => '5',
                'service_cate_name' => 'ផ្នែកវហ្វឹកហ្វឺនកូនជាង',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [

                'code' => '6',
                'service_cate_name' => 'ផ្នែកអប់រំបណ្ដុះបណ្ដាលបច្ចេកទេសនិងវិជ្ជាជីវៈ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '7',
                'service_cate_name' => 'ផ្នែកមុខរបរ និងហត្ថពលកម្ម',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '8',
                'service_cate_name' => 'ផ្នែកកម្មករនិយោជិតជាជនបរទេស',
                'service_is_ministry' => 1,
                'parent' => 0
            ],
            [
                'code' => '9',
                'service_cate_name' => 'ផ្នែកសេវារកការងារឱ្យធ្វើ',
                'service_is_ministry' => 1,
                'parent' => 0
            ],

            // Service Category Data for province
            [
                'code' => '1',
                'service_cate_name' => 'ផ្នែកអធិការកិច្ចការងារ',
                'service_is_ministry' => 0,
                'parent' => 0
            ],
            [
                'code' => '2',
                'service_cate_name' => 'ផ្នែកវិវាទការងារ',
                'service_is_ministry' => 0,
                'parent' => 0
            ],
            [
                'code' => '3',
                'service_cate_name' => 'ផ្នែកការងារកុមារ',
                'service_is_ministry' => 0,
                'parent' => 0
            ],
            [
                'code' => '4',
                'service_cate_name' => 'ផ្នែកហ្វឹកហ្វឺនកូនជាង',
                'service_is_ministry' => 0,
                'parent' => 0
            ],

            [
                'code' => '5',
                'service_cate_name' => 'ផ្នែកអប់រំបណ្ដុះបណ្ដាលបច្ចេកទេសនិងវិជ្ជាជីវៈ',
                'service_is_ministry' => 0,
                'parent' => 0
            ],

            [
                'code' => '6',
                'service_cate_name' => 'ផ្នែកមុខរបរ និងហត្ថពលកម្ម',
                'service_is_ministry' => 0,
                'parent' => 0
            ],

            // Data Parent
            [
                'code' => '1',
                'service_cate_name' => 'ការពិនិត្យកាយសម្បទាកម្មករនិយោជិតខ្មែរ',
                'service_is_ministry' => 1,
                'parent' => 1
            ],
            [
                'code' => '2',
                'service_cate_name' => 'ការពិនិត្យកាយសម្បទាកម្មករនិយោជិតជាជនបរទេស',
                'service_is_ministry' => 1,
                'parent' => 1
            ],
            [
                'code' => '1',
                'service_cate_name' => 'ការផ្ដល់ទិដ្ឋាការនិងការចុះបញ្ជិការសៀវភៅបញ្ជីចុះនាមក្មេងសម្រាប់ប្រភេទសហគ្រាស 
                គ្រឹះស្ថានដែលបម្រើឱ្យការនាំចេញ ឬធ្វើអាជិវកម្មកាស៊ីណូ ឬស្ថិតក្នុងតំបន់សេដ្ឋកិច្ចពិសេស ឬជារមណីយដ្ឋាន 
                និងសណ្ឋាគារកម្រិតចាប់ពីផ្កាយបួនឡើង ឬទីភ្នាក់ងារជ្រើសរើសឯកជន ឬស្នាក់ការកណ្ដាលគ្រឹះស្ថានធនាគារនិងហិរញ្ញវត្ថុ',
                'service_is_ministry' => 1,
                'parent' => 4
            ],
            [
                'code' => '1',
                'service_cate_name' => 'សំណុំបែបបទនិងការផ្ដល់វិញ្ញានបត្រហ្វឹកហ្វឺនកូនជាងសម្រាប់ប្រភេទសហគ្រាស 
                គ្រឹះស្ថានដែលបម្រើឱ្យការនាំចេញ ឬធ្វើងាជិវកម្មកាស៊ីណូ ឬស្ថិតក្នុងតំបន់សេដ្ឋកិច្ចពិសេស ឬជារមណីយដ្ឋាន 
                និងសណ្ឋាគារកម្រិតចាប់ពីផ្កាយបួនឡើង ឬទីភ្នាក់ងារជ្រើសរើសឬកជន ឬស្នាក់ការកណ្ដាលគ្រឹះស្ថានធនាគារនិងហិរញ្ញវត្ថុ',
                'service_is_ministry' => 1,
                'parent' => 5
            ],
            [
                'code' => '1',
                'service_cate_name' => 'ការចុះបញ្ជីបើកគ្រឹះស្ថានអប់រំបណ្ដុះបណ្ដាលបច្ចេកទេស 
                និងវិជ្ជាជីវៈកម្រិតវិញ្ញានបត្រដែលមានរយៈពេលលើសពី ៣ខែ',
                'service_is_ministry' => 1,
                'parent' => 5
            ],

            [
                'code' => '1',
                'service_cate_name' => 'ការផ្ដល់ទិដ្ឋាការ និងការចុះបញ្ជិការសៀវភៅបញ្ជីចុះនាមក្មេង 
                សម្រាប់ប្រភេទសហគ្រាស គ្រឹះស្ថានដែលមិនបម្រើឱ្យការនាំចេញ
                 ឬរមណីយដ្ឋាន និងសណ្ឋាគារកម្រិតត្រឹមផ្កាយបីចុះ ឬសាខាគ្រឹះស្ថានធនាគារ និងហិរញ្ញវត្ថុ',
                'service_is_ministry' => 0,
                'parent' => 12
            ],

            [
                'code' => '1',
                'service_cate_name' => 'សំណុំបែបបទ និងការផ្ដល់វិញ្ញានបត្រហ្វឹកហ្វឺនកូនជាងសម្រាប់ប្រភេទសហគ្រាស គ្រឹះស្ថានដែលមិនបម្រើឱ្យការនាំចេញ 
                ឬរមណីយដ្ឋាន និងរមណីយដ្ឋាននិងសណ្ឋាគារកម្រិតត្រឹមផ្កាយបីចុះ ឬសាខាគ្រឹះស្ថានធនាគារ និងហិរញ្ញវត្ថុ',
                'service_is_ministry' => 0,
                'parent' => 13
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_through_partner_banks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('name_kh');
            $table->string('name_en');
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::Table('payment_through_partner_banks')->insert([

            ['code' => '0001','name_kh' => 'ធនាគារ អេស៊ីលីដា', 'name_en' => 'Acleda', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '0002','name_kh' => 'ធនាគារ វឌ្ឍនៈ អាស៊ី ចំកាត់​', 'name_en' => 'ABA', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '0003','name_kh' => 'ធនាគារ​ ស្ថាបនា (អេប៊ីអេ)', 'name_en' => 'Sathapana (ABA)', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '0004','name_kh' => 'ធនាគារ វឌ្ឍនៈ', 'name_en' => 'Vattanac', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '0005','name_kh' => 'ធនាគារ វីង', 'name_en' => 'Wing', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '0006','name_kh' => 'ធនាគារ ហត្ថា', 'name_en' => 'Hatha', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '0007','name_kh' => 'ធនាគារ ជីប ម៉ុង', 'name_en' => 'Chipmong', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '0008','name_kh' => 'ធនាគារ កាណាឌីយ៉ា', 'name_en' => 'Canadia', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '0009','name_kh' => 'ធនាគារ ភ្នំពេញ ពាណិជ្ជ', 'name_en' => 'PPCBank (ABA)', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '0010','name_kh' => 'ប្រមូលផ្ទាល់', 'name_en' => 'ប្រមូលផ្ទាល់', 'description' => '',  'created_at' => now(), 'updated_at' => now()],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_through_partner_banks');
    }
};

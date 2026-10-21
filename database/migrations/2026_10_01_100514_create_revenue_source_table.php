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
        Schema::create('revenue_source', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('name', 25);
            $table->string('description');
            $table->timestamps();
        });
        DB::Table('revenue_source')->insert([

            ['code' => '1','name' => 'FWCMS', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '2','name' => 'CamDX', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '3','name' => 'LACMS', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '4','name' => 'e-Payment', 'description' => '',  'created_at' => now(), 'updated_at' => now()],
            ['code' => '5','name' => 'ប្រមូលផ្ទាល់', 'description' => '',  'created_at' => now(), 'updated_at' => now()],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_source');
    }
};

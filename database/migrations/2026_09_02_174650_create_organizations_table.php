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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('parent_id')->nullable();
            $table->string('sub_parent_id')->nullable();
            $table->timestamps();
        });

        DB::table('organizations')->insert([
            ['name' => 'រដ្ខមន្ត្រី', 'parent_id' => null],

            ['name' => 'ខុទ្ធកាល័យ', 'parent_id' => 1],
            ['name' => 'រដ្ឋលេខាធិការ', 'parent_id' => 1],
            ['name' => 'អគ្គនាយកដ្ឋានសវនកម្មផ្ទៃក្នុង', 'parent_id' => 1],     
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

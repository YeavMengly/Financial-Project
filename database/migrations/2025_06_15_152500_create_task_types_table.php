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
        Schema::create('task_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('task_types')->insert([
            [
                'name' => 'រជ្ចទេយ្យ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'លទ្ធកម្ម',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'បើកផ្ដល់មុន',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ទូទាត់ត្រង់',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'បុរេប្រទាន',
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
        Schema::dropIfExists('task_types');
    }
};

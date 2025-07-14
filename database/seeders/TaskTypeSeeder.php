<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        /*
        |-------------------------------------------------------------------------------
        |  Clear existing data
        |-------------------------------------------------------------------------------
        */
        DB::table('task_types')->truncate();

        /*
        |-------------------------------------------------------------------------------
        | Insert data into mission_types table
        |-------------------------------------------------------------------------------
        */
        DB::table('task_types')->insert([
            [
                'task' => 'រជ្ចទេយ្យ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task' => 'លទ្ធកម្ម',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task' => 'បើកផ្ដល់មុន',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task' => 'ទូទាត់ត្រង់',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task' => 'បុរេប្រទាន',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

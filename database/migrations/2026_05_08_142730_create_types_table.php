<?php

use App\Models\Content\Type;
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
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->nullable();
            $table->string('number_type')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        $now  = now();
        Type::insert([
            [
                'code' => '1',
                'number_type' => 'ប្រភេទ១',
                'name' => 'បន្ទុកបុគ្គលិក',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'code' => '2',
                'number_type' => 'ប្រភេទ២',
                'name' => 'ចំណាយប្រតិបត្តិការ',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'code' => '4',
                'number_type' => 'ប្រភេទ៤',
                'name' => 'ចំណាយវិនិយោគ',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'code' => '5',
                'number_type' => 'ប្រភេទ៥',
                'name' => 'ការផ្ទេរ',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'code' => '6',
                'number_type' => 'ប្រភេទ៦',
                'name' => 'ចំណាយផ្សេងៗ',
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};

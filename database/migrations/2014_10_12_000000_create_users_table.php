<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('role_id');
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->json('permissions')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        $now  = now();
        $data = [
            "role_id" => 1,
            "username"  => "yeavmengly",
            "email"  => "yeavmengly@gmail.com",
            "password"  => bcrypt('1234567890'),
            'created_at'        => $now,
            'updated_at'        => $now,
        ];

        User::insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

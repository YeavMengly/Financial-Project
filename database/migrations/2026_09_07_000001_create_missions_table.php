<?php

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
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('ច')->constrained('ministries')->cascadeOnDelete();

            $table->unsignedBigInteger('ministry_id');
            // $table->integer('program_id', 4)->default(0);
            // $table->integer('program_sub_id', 4)->default(0);
            // $table->integer('cluster_id', 4)->default(0);
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('program_sub_id')->nullable();
            $table->unsignedBigInteger('cluster_id')->nullable();

            // $table->integer('account_sub_id', 4)->default(0);
            $table->unsignedBigInteger('document_id');
            $table->integer('leader_id')->default(0);

            $table->unsignedBigInteger('province_id');
            // $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->string('legal_number');
            $table->date('legal_date');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count')->default(0);
            $table->integer('nights_count')->default(0);
            $table->enum('mission_type', ['local', 'abroad']);
            $table->integer('mission_type_is_archived')->default(1);
            $table->string('fileName')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid']);
            $table->integer('payment_is_archived')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};

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
        Schema::create('candidates', function (Blueprint $table) {
            $table->string('Idcode')->primary();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('Fullname');
            $table->string('Image')->default('storage/default/user.png');
            $table->date('DOB');
            $table->string('Address')->nullable();
            $table->string('Examination_room');
            $table->string('Password');
            $table->string('Email')->unique();
            $table->enum('Status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};

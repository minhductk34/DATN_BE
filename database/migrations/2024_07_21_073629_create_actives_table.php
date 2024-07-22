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
        Schema::create('actives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_subject_id')->constrained('exam_subjects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('Idcode');
            $table->foreign('Idcode')->references('Idcode')->on('candidates')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('Active',['Active','Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actives');
    }
};

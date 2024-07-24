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
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->string('exam_subject_id');
            $table->foreign('exam_subject_id')->references('id')->on('exam_subjects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('Idcode');
            $table->foreign('Idcode')->references('Idcode')->on('candidates')->cascadeOnDelete()->cascadeOnUpdate();
            $table->float('Point', 5, 2);
            $table->integer('Number_of_correct_sentences')->default(0);
            $table->dateTime('TimeStart');
            $table->dateTime('TimeEnd');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};

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
        Schema::create('candidate_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('idcode')->nullable();
            $table->foreign('idcode')->references('idcode')->on('candidates')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('subject_id')->nullable();
            $table->integer('numerical_order')->nullable();
            $table->string('answer_P')->nullable();
            $table->string('answer_Temp')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_questions');
    }
};

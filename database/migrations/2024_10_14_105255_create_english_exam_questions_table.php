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
        Schema::create('english_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_id');
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('reading_id');
            $table->foreign('reading_id')->references('id')->on('readings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('listening_id');
            $table->foreign('listening_id')->references('id')->on('listenings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('idcode')->nullable();
            $table->foreign('idcode')->references('idcode')->on('candidates')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('numerical_order');
            $table->string('answer_P');
            $table->string('answer_Pi');
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
        Schema::dropIfExists('english_exam_questions');
    }
};

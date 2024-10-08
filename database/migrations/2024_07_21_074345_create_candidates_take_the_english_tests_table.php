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
        Schema::create('candidates_take_the__english_tests', function (Blueprint $table) {
            $table->id();
            $table->string('question_id');
            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('reading_id');
            $table->foreign('reading_id')->references('id')->on('readings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('listening_id');
            $table->foreign('listening_id')->references('id')->on('listenings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('Idcode');
            $table->foreign('Idcode')->references('Idcode')->on('candidates')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('Numerical_order');
            $table->string('Answer_P');
            $table->string('Answer_Pi');
            $table->string('Answer_Temp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates_take_the__english_tests');
    }
};

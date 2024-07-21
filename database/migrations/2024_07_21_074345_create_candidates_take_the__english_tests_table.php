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
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('reading_id')->constrained('readings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('listening_id')->constrained('listenings')->cascadeOnDelete()->cascadeOnUpdate();
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

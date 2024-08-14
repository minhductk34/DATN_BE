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
        Schema::create('reading_questions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('reading_id');
            $table->foreign('reading_id')->references('id')->on('readings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('Title');
            $table->string('Answer_P');
            $table->string('Answer_F1');
            $table->string('Answer_F2');
            $table->string('Answer_F3');
            $table->enum('Status', ['true', 'false'])->default('true');
            $table->enum('Level',['Easy','Medium','Difficult'])->default('Easy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_questions');
    }
};

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
        Schema::create('reading_question_versions', function (Blueprint $table) {
            $table->id();
            $table->string('question_id');
            $table->foreign('question_id')->references('id')->on('reading_questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('version');
            $table->string('Title');
            $table->string('Answer_P')->nullable();
            $table->string('Answer_F1')->nullable();
            $table->string('Answer_F2')->nullable();
            $table->string('Answer_F3')->nullable();
            $table->enum('Status', ['true', 'false'])->default('true');
            $table->enum('Level',['Easy','Medium','Difficult'])->default('Easy');
            $table->unique(['question_id', 'version']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_question_versions');
    }
};

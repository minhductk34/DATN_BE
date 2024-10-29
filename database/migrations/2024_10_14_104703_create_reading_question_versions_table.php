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
            $table->string('reading_question_id');
            $table->foreign('reading_question_id')->references('id')->on('reading_questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('version');
            $table->string('title');
            $table->string('answer_P')->nullable();
            $table->string('answer_F1')->nullable();
            $table->string('answer_F2')->nullable();
            $table->string('answer_F3')->nullable();
            $table->boolean('status')->default(true);
            $table->enum('level',['easy','medium','difficult'])->default('easy');
            $table->unique(['reading_question_id', 'version']);
            $table->timestamps();
            $table->softDeletes();
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

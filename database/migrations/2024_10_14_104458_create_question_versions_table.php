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
        Schema::create('question_versions', function (Blueprint $table) {
            $table->id();
            $table->string('question_id');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->string('title');
            $table->string('image_title')->nullable();
            $table->string('answer_P');
            $table->string('image_P')->nullable();
            $table->string('answer_F1');
            $table->string('image_F1')->nullable();
            $table->string('answer_F2');
            $table->string('image_F2')->nullable();
            $table->string('answer_F3');
            $table->string('image_F3')->nullable();
            $table->enum('level', ['easy', 'medium', 'difficult'])->default('easy');
            $table->integer('version');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['question_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_versions');
    }
};

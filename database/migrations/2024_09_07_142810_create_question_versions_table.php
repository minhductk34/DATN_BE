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
            $table->string('Title');
            $table->string('Image_Title')->nullable();
            $table->string('Answer_P');
            $table->string('Image_P')->nullable();
            $table->string('Answer_F1');
            $table->string('Image_F1')->nullable();
            $table->string('Answer_F2');
            $table->string('Image_F2')->nullable();
            $table->string('Answer_F3');
            $table->string('Image_F3')->nullable();
            $table->enum('Level', ['Easy', 'Medium', 'Difficult'])->default('Easy');
            $table->integer('version');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

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

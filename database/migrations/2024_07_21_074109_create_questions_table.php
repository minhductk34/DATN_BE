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
        Schema::create('questions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('exam_content_id');
            $table->foreign('exam_content_id')->references('id')->on('exam_contents')->cascadeOnDelete()->cascadeOnUpdate();            $table->string('Title');
            $table->string('Image_Title')->nullable();
            $table->string('Answer_P');
            $table->string('Image_P')->nullable();
            $table->string('Answer_F1');
            $table->string('Image_F1')->nullable();
            $table->string('Answer_F2');
            $table->string('Image_F2')->nullable();
            $table->string('Answer_F3');
            $table->string('Image_F3')->nullable();
            $table->enum('Level',['Easy','Medium','Difficult'])->default('Easy');
            $table->enum('Status', ['true', 'false'])->default('true');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

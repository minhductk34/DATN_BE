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
        Schema::create('exam_contents', function (Blueprint $table) {
            $table->id();
            $table->string('exam_subject_id');
            $table->foreign('exam_subject_id')->references('id')->on('exam_subjects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->enum('Status', ['true', 'false'])->default('true');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_contents');
    }
};

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
        Schema::create('exam_structures', function (Blueprint $table) {
            $table->id();
            $table->string('exam_subject_id');
            $table->foreign('exam_subject_id')->references('id')->on('exam_subjects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('exam_content_id');
            $table->foreign('exam_content_id')->references('id')->on('exam_contents')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('level',['easy', 'medium', 'difficult'])->default('easy');
            $table->smallInteger('quantity');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_structures');
    }
};

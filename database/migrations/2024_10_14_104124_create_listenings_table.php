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
        Schema::create('listenings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('exam_content_id');
            $table->foreign('exam_content_id')->references('id')->on('exam_contents')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('audio');
            $table->boolean('status')->default(true);
            $table->enum('level',['easy', 'medium', 'difficult']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listenings');
    }
};

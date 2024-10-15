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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->string('exam_subject_id');
            $table->foreign('exam_subject_id')->references('id')->on('exam_subjects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('idcode');
            $table->foreign('idcode')->references('idcode')->on('candidates')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('answer');
            $table->dateTime('time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};

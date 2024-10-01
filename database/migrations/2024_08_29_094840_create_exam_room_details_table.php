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
        Schema::create('exam_room_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_room_id');
            $table->foreign('exam_room_id')->references('id')->on('exam_rooms')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('exam_subject_id');
            $table->foreign('exam_subject_id')->references('id')->on('exam_subjects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('exam_session_id');
            $table->foreign('exam_session_id')->references('id')->on('exam_sessions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('exam_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_room_details');
    }
};

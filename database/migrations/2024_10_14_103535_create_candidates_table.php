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
        Schema::create('candidates', function (Blueprint $table) {
            $table->string('idcode')->primary();
            $table->unsignedBigInteger('exam_room_id');
            $table->foreign('exam_room_id')->references('id')->on('exam_rooms')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('exam_id');
            $table->foreign('exam_id')->references('id')->on('exams')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('image')->default('storage/default/user.png');
            $table->date('dob');
            $table->string('address')->nullable();
            $table->string('email')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};

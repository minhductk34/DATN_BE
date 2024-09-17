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
        Schema::create('exam_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('poetry_id');
            $table->foreign('poetry_id')->references('id')->on('poetries')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('Name');
            $table->integer('Quantity');
            $table->enum('Status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_rooms');
    }
};

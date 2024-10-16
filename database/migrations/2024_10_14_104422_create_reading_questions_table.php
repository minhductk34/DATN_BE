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
        Schema::create('reading_questions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('reading_id')->nullable();
            $table->foreign('reading_id')->references('id')->on('readings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('current_version_id')->nullable();
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
        Schema::dropIfExists('reading_questions');
    }
};

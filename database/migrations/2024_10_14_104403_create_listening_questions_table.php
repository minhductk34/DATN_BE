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
        Schema::create('listening_questions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('listening_id');
            $table->foreign('listening_id')->references('id')->on('listenings')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('listening_questions');
    }
};

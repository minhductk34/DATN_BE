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
            $table->foreign('listening_id')->references('id')->on('listenings')->cascadeOnDelete()->cascadeOnUpdate();            $table->string('Title');
            $table->string('Answer_P')->nullable();
            $table->string('Answer_F1')->nullable();
            $table->string('Answer_F2')->nullable();
            $table->string('Answer_F3')->nullable();
            $table->enum('Status', ['true', 'false'])->default('true');
            $table->enum('Level',['Easy','Medium','Difficult'])->default('Easy');
            $table->timestamps();
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

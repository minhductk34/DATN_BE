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
        Schema::create('listening_question_versions', function (Blueprint $table) {
            $table->id();
            $table->string('listening_question_id');
            $table->foreign('listening_question_id')->references('id')->on('listening_questions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('version');
            $table->string('title');
            $table->string('answer_P')->nullable();
            $table->string('answer_F1')->nullable();
            $table->string('answer_F2')->nullable();
            $table->string('answer_F3')->nullable();
            $table->enum('status', [true,false])->default(true);
            $table->enum('level',['easy','medium','difficult'])->default('easy');
            $table->unique(['listening_question_id', 'version']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listening_question_versions');
    }
};

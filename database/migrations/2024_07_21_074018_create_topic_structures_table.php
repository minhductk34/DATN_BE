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
        Schema::create('topic_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_content_id')->constrained('exam_contents')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('Level',['Easy','Medium','Difficult'])->default('Easy');
            $table->smallInteger('Quality');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_structures');
    }
};

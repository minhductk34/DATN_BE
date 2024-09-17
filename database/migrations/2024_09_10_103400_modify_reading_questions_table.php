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
        Schema::table('reading_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('current_version_id')->nullable()->after('id');
            $table->dropColumn(['Title', 'Answer_P', 'Answer_F1', 'Answer_F2', 'Answer_F3', 'Level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reading_questions', function (Blueprint $table) {
            $table->dropColumn('current_version_id');
            $table->string('Title');
            $table->string('Answer_P');
            $table->string('Answer_F1');
            $table->string('Answer_F2');
            $table->string('Answer_F3');
            $table->enum('Level', ['Easy', 'Medium', 'Difficult'])->default('Easy');
        });
    }
};

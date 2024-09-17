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
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('current_version_id')->nullable()->after('id');
            $table->dropColumn(['Title', 'Image_Title', 'Answer_P', 'Image_P', 'Answer_F1', 'Image_F1', 'Answer_F2', 'Image_F2', 'Answer_F3', 'Image_F3', 'Level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('current_version_id');
            $table->string('Title');
            $table->string('Image_Title')->nullable();
            $table->string('Answer_P');
            $table->string('Image_P')->nullable();
            $table->string('Answer_F1');
            $table->string('Image_F1')->nullable();
            $table->string('Answer_F2');
            $table->string('Image_F2')->nullable();
            $table->string('Answer_F3');
            $table->string('Image_F3')->nullable();
            $table->enum('Level', ['Easy', 'Medium', 'Difficult'])->default('Easy');
        }); 
    }
};

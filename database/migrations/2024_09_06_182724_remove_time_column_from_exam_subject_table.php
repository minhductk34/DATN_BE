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
        Schema::table('exam_subjects', function (Blueprint $table) {
            $table->dropColumn('TimeStart');
            $table->dropColumn('TimeEnd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_subjects', function (Blueprint $table) {
            $table->dateTime('TimeStart');
            $table->dateTime('TimeEnd');
        });
    }
};

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
        Schema::table('listenings', function (Blueprint $table) {
            $table->dropColumn('url_listenning');
            $table->string('Name')->after('exam_content_id');
            $table->string('Audio')->after('Name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listenings', function (Blueprint $table) {
            $table->string('url_listenning')->after('exam_content_id');
            $table->dropColumn('Name');
            $table->dropColumn('Audio');
        });
    }
};

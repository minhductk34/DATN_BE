<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('topic_structures', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('topic_structures', function (Blueprint $table) {
            $table->renameColumn('id', 'old_id');
        });

        Schema::table('topic_structures', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)->first();
        });

        Schema::table('topic_structures', function (Blueprint $table) {
            $table->dropColumn('old_id');
        });
    }

    public function down()
    {
        Schema::table('topic_structures', function (Blueprint $table) {
            $table->dropPrimary();

            $table->renameColumn('id', 'temp_id');

            $table->string('id')->first();

            $table->primary('id');
        });

        Schema::table('topic_structures', function (Blueprint $table) {
            $table->dropColumn('temp_id');
        });
    }
};
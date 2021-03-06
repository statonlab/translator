<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueConstraintToTranslatedLinesTableForKeyFileId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('translated_lines', function (Blueprint $table) {
            $table->unique([
                'key',
                'file_id',
                'language_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('translated_lines', function (Blueprint $table) {
            $table->dropUnique([
                'key',
                'file_id',
                'language_id'
            ]);
        });
    }
}

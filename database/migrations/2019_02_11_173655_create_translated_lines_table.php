<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslatedLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translated_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('Last modified by this user');
            $table->unsignedInteger('serialized_line_id');
            $table->unsignedInteger('language_id');
            $table->string('value')->nullable()->index();
            $table->timestamps();

            $table->foreign('serialized_line_id')
                ->references('id')
                ->on('serialized_lines')
                ->onDelete('cascade');

            $table->foreign('language_id')
                ->references('id')
                ->on('languages')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translated_lines');
    }
}

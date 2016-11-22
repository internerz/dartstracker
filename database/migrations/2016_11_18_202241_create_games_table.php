<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('mode_id')->unsigned();
            $table->integer('ruleset')->unsigned();
            $table->integer('number_of_legs_to_win')->unsigned();
            $table->integer('winner_user_id')->unsigned()->nullable();
            $table->foreign('mode_id')->references('id')->on('modes');
            $table->foreign('winner_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}

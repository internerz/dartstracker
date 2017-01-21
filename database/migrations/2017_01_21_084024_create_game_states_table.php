<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameStatesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_state', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned();
            $table->integer('state_id')->unsigned();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_state');
    }
}

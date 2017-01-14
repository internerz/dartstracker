<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLegUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leg_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('leg_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('leg_id')->references('id')->on('legs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leg_user');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('modes')->insert(
            array(
                'name' => '501',
            )
        );

        DB::table('modes')->insert(
            array(
                'name' => '301',
            )
        );

        DB::table('modes')->insert(
            array(
                'name' => 'Cricket',
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modes');
    }
}

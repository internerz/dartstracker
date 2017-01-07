<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modes', function(Blueprint $table) {
            $table->integer('score')->unsigned();
        });

        DB::table('modes')
            ->where('name', '501')
            ->update(['score' => 501]);

        DB::table('modes')
            ->where('name', '301')
            ->update(['score' => 301]);

        DB::table('modes')
            ->where('name', 'Cricket')
            ->update(['score' => 0]);

        DB::table('modes')->insert(
            array(
                'name' => '701',
                'score' => 701
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
        //
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStatesTableV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('states', function(Blueprint $table){
            $table->string('phase');
        });

        DB::table('states')
            ->where('name', 'DoubleIn')
            ->update(['phase' => 'Start']);

        DB::table('states')
            ->where('name', 'DoubleOut')
            ->update(['phase' => 'End']);

        DB::table('states')
            ->where('name', 'Playing')
            ->update(['phase' => 'Playing']);

        DB::table('states')->insert(
            array(
                'name' => 'TrippleOut',
                'phase' => 'End'
            )
        );

        DB::table('states')->insert(
            array(
                'name' => 'BullsOut',
                'phase' => 'End'
            )
        );

        DB::table('states')->insert(
            array(
                'name' => 'SingleIn',
                'phase' => 'Start'
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
        Schema::dropIfExists('states');
    }
}

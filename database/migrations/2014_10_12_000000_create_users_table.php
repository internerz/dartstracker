<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('admin')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert(
            array(
                'name' => 'Felix Rodriquez',
                'email' => 'user1@test.de',
                'password' => bcrypt('test'),
                'admin' => true,
            )
        );

        DB::table('users')->insert(
            array(
                'name' => 'Albert Simmons',
                'email' => 'user2@test.de',
                'password' => bcrypt('test'),
            )
        );

        DB::table('users')->insert(
            array(
                'name' => 'Ralph Fleming',
                'email' => 'user3@test.de',
                'password' => bcrypt('test'),
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
        Schema::drop('users');
    }
}

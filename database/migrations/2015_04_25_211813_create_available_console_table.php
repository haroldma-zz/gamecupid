<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailableConsoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('available_console', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id');
            $table->integer('console_id');
            $table->date('release_date');
            $table->string('region')->nullable();
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('available_console');
	}

}

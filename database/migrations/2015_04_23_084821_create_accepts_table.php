<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcceptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accepts', function(Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('state');
            $table->mediumText('message');
            $table->integer('user_id');
            $table->integer('post_id');
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
		Schema::drop('accepts');
	}

}

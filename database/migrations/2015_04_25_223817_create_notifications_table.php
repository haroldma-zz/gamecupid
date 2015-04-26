<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->mediumText('description');
            $table->integer('to_id');
            $table->integer('from_id');
            $table->tinyInteger('read');
            $table->tinyInteger('notified');
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
		Schema::drop('notifications');
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invites', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->mediumText('self_text');
            $table->mediumText('markdown_text');
            $table->string('tag_text');
            $table->string('slug');
            $table->integer('player_count');
            $table->tinyInteger('featured');
            $table->integer('user_id');
            $table->integer('game_id');
            $table->integer('console_id');

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
		Schema::drop('invites');
	}

}

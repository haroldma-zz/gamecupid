<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('posts', function(Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type');
            $table->tinyInteger('is_self_text');
            $table->tinyInteger('category');
            $table->string('title');
            $table->string('slug');
            $table->mediumText('markdown_text');
            $table->mediumText('self_text');
            $table->string('flair_text');
            $table->integer('max_players');
            $table->tinyInteger('featured');
            $table->tinyInteger('verified_only');
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
		Schema::drop('posts');
	}

}

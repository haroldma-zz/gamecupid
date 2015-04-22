<?php namespace App\Http\Controllers;

use Schema;

class TempController extends Controller {

	/**
	*
	* The "makedb" function; This function creates all the tables in our database.
	*
	**/
	public function makedb()
	{
		/**
		*
		* Drop tables if they exist
		*
		**/
		Schema::dropIfExists('users');
		Schema::dropIfExists('profiles');
		Schema::dropIfExists('platforms');
		Schema::dropIfExists('consoles');
		Schema::dropIfExists('invites');
		Schema::dropIfExists('accepts');
		Schema::dropIfExists('games');
		Schema::dropIfExists('publishers');
		Schema::dropIfExists('developers');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');


		/**
		*
		* The users table
		* Model: User.php
		*
		**/
		Schema::create('users', function($table)
		{
		    $table->increments('id');
		    $table->string('first_name', 100);
		    $table->string('last_name', 100);
		    $table->string('email', 150);
		    $table->string('password', 100);
		    $table->tinyInteger('banned');
		    $table->tinyInteger('is_private');
		    $table->date('dob');
		    $table->softDeletes();
		    $table->rememberToken();
		    $table->timestamps();
		});

		/**
		*
		* The gaming profiles table
		* Model: Profile.php
		*
		**/
		Schema::create('profiles', function($table)
		{
			$table->increments('id');
			$table->string('online_id');
			$table->integer('platform_id');
			$table->integer('user_id');
			$table->timestamps();
		});

		/**
		*
		* The platforms table
		* Model: Platform.php
		*
		**/
		Schema::create('platforms', function($table)
		{
			$table->increments('id');
			$table->string('name', 50);
			$table->mediumText('description');
			$table->string('logo_url');
			$table->timestamps();
		});

		/**
		*
		* The consoles table
		* Model: Console.php
		*
		**/
		Schema::create('consoles', function($table)
		{
			$table->increments('id');
			$table->string('name', 50);
			$table->mediumText('description');
			$table->string('logo_url');
			$table->date('release_date');
			$table->integer('platform_id');
			$table->timestamps();
		});

		/**
		*
		* The invites table
		* Model: Invite.php
		*
		**/
		Schema::create('invites', function($table)
		{
			$table->increments('id');
			$table->string('title', 100);
			$table->mediumText('self_text');
			$table->string('tag_text');
			$table->string('slug');
			$table->tinyInteger('requires_approval');
			$table->datetime('starts');
			$table->datetime('expires');
			$table->tinyInteger('featured');
			$table->integer('user_id');
			$table->integer('game_id');
			$table->integer('console_id');
			$table->integer('platform_id');
			$table->softDeletes();
			$table->timestamps();
		});

		/**
		*
		* The accepts table (userA invites, userB accepts invite)
		* Model: Accept.php
		*
		**/
		Schema::create('accepts', function($table)
		{
			$table->increments('id');
			$table->tinyInteger('state');
			$table->mediumText('message');
			$table->integer('user_id');
			$table->integer('invite_id');
			$table->timestamps();
		});

		/**
		*
		* The games table
		* Model: Game.php
		*
		**/
		Schema::create('games', function($table)
		{
			$table->increments('id');
			$table->string('name', 75);
			$table->mediumText('description');
			$table->string('box_art_url');
			$table->string('logo_url');
			$table->string('clear_art_url');
			$table->string('backdrop_url');
			$table->date('release_date');
			$table->string('slug');
			$table->tinyInteger('publisher_id');
			$table->tinyInteger('developer_id');
			$table->timestamps();
		});

		/**
		*
		* The publishers table
		* Model: Publisher.php
		*
		**/
		Schema::create('publishers', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('logo_url');
			$table->string('website_url');
			$table->string('slug');
			$table->timestamps();
		});

		/**
		*
		* The developers table
		* Model: Developer.php
		*
		**/
		Schema::create('developers', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('logo_url');
			$table->string('website_url');
			$table->string('slug');
			$table->timestamps();
		});

		/**
		*
		* Entrust package migrations
		*
		**/
        // Create table for storing roles
        Schema::create('roles', function ($table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('role_user', function ($table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        // Create table for storing permissions
        Schema::create('permissions', function ($table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('permission_role', function ($table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')->references('id')->on('permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

	}

}























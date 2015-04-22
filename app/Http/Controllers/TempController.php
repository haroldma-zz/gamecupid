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
		    $table->timestamps();
		    $table->rememberToken();
		});

		/**
		*
		* The profiles table
		* Model: Profile.php
		*
		**/
		Schema::create('profiles', function($table)
		{
			$table->increments('id');
			$table->tinyInteger('type');

			// This table will have more columns, we need to figure this out.

			$table->integer('user_id');
			$table->timestamps();
		});
	}

}
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
        Schema::dropIfExists('reps');
        Schema::dropIfExists('rep_events');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('migrations');
	}
}























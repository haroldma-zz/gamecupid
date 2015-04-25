<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use \Hash;

class UsersTableSeeder extends Seeder {

    public function run()
    {
		$now  = date('Y-m-d h:i:s');
		$user = new User;

		$user->first_name = "John";
		$user->last_name  = "Doe";
		$user->username   = "johnny_doe";
		$user->email      = "john@doe.com";
		$user->password   = Hash::make('test1234');
		$user->banned     = false;
		$user->is_private = false;
		$user->dob        = date('Y-m-d', strtotime($now));

		$user->save();
    }

}
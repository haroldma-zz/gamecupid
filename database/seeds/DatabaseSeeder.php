<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call("PlatformsTableSeeder");
		$this->call("ConsolesTableSeeder");
		$this->call("RepEventsTableSeeder");
		$this->call("GamesTableSeeder");
		$this->call("AvailableConsoleTableSeeder");
	}

}

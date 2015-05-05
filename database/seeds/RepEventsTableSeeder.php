<?php

use Illuminate\Database\Seeder;
use App\Models\RepEvent;
use \Hash;

class RepEventsTableSeeder extends Seeder {

    public function run()
    {
		$e         = new RepEvent;
		$e->event  = "Registered as a user of GameCupid.";
		$e->amount = 1;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "Confirmed your e-mail address.";
		$e->amount = 3;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "Added a profile picture.";
		$e->amount = 3;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "Added a bio.";
		$e->amount = 3;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "Completed your profile.";
		$e->amount = 10;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "Submitted a post.";
		$e->amount = -1;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "You got approved to an post.";
		$e->amount = 2;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "You didn't show up in-game.";
		$e->amount = -3;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "Created a crew.";
		$e->amount = 5;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "Joined a crew.";
		$e->amount = 10;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "Left a crew.";
		$e->amount = -5;
		$e->save();

		$e         = new RepEvent;
		$e->event  = "Verified profile.";
		$e->amount = 2;
		$e->save();
    }

}
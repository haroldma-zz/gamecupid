<?php

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Developer;
use App\Models\Publisher;
use App\Models\GameDeveloper;
use App\Models\GamePublisher;
use Cocur\Slugify\Slugify;

class GamesTableSeeder extends Seeder {

    public function run()
    {
        $slugify = new Slugify();
        $slugify->addRule('+', 'plus');

		$json = json_decode(file_get_contents(base_path() . '/database/seeds/games.json'));

		foreach($json as $game)
		{
			$g               = new Game;
			$g->title        = $game->title;
			$g->slug         = $slugify->slugify($game->title, "-");
			$g->description  = $game->description;
			$g->poster       = $game->poster;
			$g->series       = $game->series;
			$g->trailer      = $game->youtubeTrailer;
			$g->release_date = $game->releases[0]->date;
			$g->save();

			foreach ($game->developers as $d)
			{
				$dev = Developer::where('slug', $slugify->slugify($d->name))->first();

				if (!$dev)
				{
					$dev       = new Developer;
					$dev->name = $d->name;
					$dev->slug = $slugify->slugify($d->name);
					$dev->save();
				}

				$gameDev               = new GameDeveloper;
				$gameDev->game_id      = $g->id;
				$gameDev->developer_id = $dev->id;
				$gameDev->save();
			}

			foreach ($game->publishers as $d)
			{
				$pub = Publisher::where('slug', $slugify->slugify($d->name))->first();

				if (!$pub)
				{
					$pub       = new Publisher;
					$pub->name = $d->name;
					$pub->slug = $slugify->slugify($d->name);
					$pub->save();
				}

				$gamePub               = new GamePublisher;
				$gamePub->game_id      = $g->id;
				$gamePub->publisher_id = $pub->id;
				$gamePub->save();
			}
		}
    }

}
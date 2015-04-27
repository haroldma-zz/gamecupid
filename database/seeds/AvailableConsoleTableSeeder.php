<?php

use App\Models\Game;
use Cocur\Slugify\Slugify;
use Illuminate\Database\Seeder;
use App\Models\AvailableConsole;

class AvailableConsoleTableSeeder extends Seeder {

    public function run()
    {
        $slugify = new Slugify();
        $slugify->addRule('+', 'plus');

		$json = json_decode(file_get_contents(base_path() . '/database/seeds/games.json'));

		foreach($json as $game)
		{
			$g = Game::where('slug', $slugify->slugify($game->title))->first();

			if ($g)
			{
				foreach($game->releases as $release)
				{
					switch ($release->name) {
						case 'xbox-360':
							$console_id = 1;
							break;

						case 'xbox-one':
							$console_id = 2;
							break;

						case 'playstation-3':
							$console_id = 3;
							break;

						case 'playstation-4':
							$console_id = 4;
							break;

						default:
							$console_id = 5;
							break;
					}

					$aC               = new AvailableConsole;
					$aC->console_id   = $console_id;
					$aC->game_id      = $g->id;
					$aC->release_date = $release->date;
					$aC->region       = ($release->region == '' ? null : $release->region);
					$aC->save();
				}
			}
		}
    }

}
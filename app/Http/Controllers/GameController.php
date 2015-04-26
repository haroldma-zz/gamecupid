<?php namespace App\Http\Controllers;

use Response;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller {

	/**
	*
	* Search game
	*
	**/
	public function search(Request $request)
	{
		if ($request->ajax())
		{
			if (!$request->get('title'))
			{
				return Response::make('no input', 500);
			}

			$results = Game::where('title', 'LIKE', $request->get('title') . '%')->orderBy('title', 'ASC')->get();

			return Response::make($results, 200);
		}
	}

}
<?php namespace App\Http\Controllers;

use Response;
use App\Models\Game;
use Illuminate\Http\Request;
use Kumuwai\DataTransferObject\Laravel5DTO;

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

			$results = gameSearchResultsToDto(Game::where('title', 'LIKE', $request->get('title') . '%')->orderBy('title', 'ASC')->get());

			return Response::make($results, 200);
		}
	}


	/**
	*
	* Return consoles for given hashId (invite form)
	*
	**/
	public function formConsoles(Request $request)
	{
		$id  = decodeHashId($request->input('id'));
		$dto = [];

		$consoles = Game::find($id)->consoles;


		foreach ($consoles as $console)
		{
			if (isset($console->console->id))
			{
				$dto[] = [
					'id'   => hashId($console->console->id),
					'name' => $console->console->name
				];
			}
		}

		return new Laravel5DTO($dto);;
	}

}
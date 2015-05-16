<?php namespace App\Http\Controllers;

use App\Models\Requestt;
use App\Models\GameSession;
use App\Enums\RequestStates;
use App\Enums\GameSessionStates;

class SessionController extends Controller {

	/**
	*
	* Accept an invite request
	*
	**/
	public function acceptInviteRequest($username, $requestId)
	{
		$request = Requestt::find(decodeHashId($requestId));

		if ($request->state == RequestStates::PENDING)
			$request->state = RequestStates::ACCEPTED;
		else
			return redirect()->back();

		if ($request->save())
		{
			# Create gameSession
			$session          = new GameSession;
			$session->post_id = $request->post_id;
			$session->state   = GameSessionStates::PLAYING;

			if ($session->save())
				return redirect('/' . $username . '/session/' . $requestId);
			else
				echo 'Something went wrong, try again.';
		}
		else
		{
			echo 'Something went wrong, go back and try again.';
		}
	}

	/**
	*
	* Decline an invite request
	*
	**/
	public function declineInviteRequest($username, $requestId)
	{
		$request = Requestt::find(decodeHashId($requestId));

		if ($request->state == RequestStates::PENDING)
			$request->state = RequestStates::DECLINED;
		else
			return redirect()->back();

		if ($request->save())
		{
			return redirect()->back();
		}
		else
		{
			echo 'Something went wrong, go back and try again.';
		}
	}

}
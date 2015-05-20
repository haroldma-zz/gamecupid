<?php namespace App\Http\Controllers;

use Auth;
use App\Models\Post;
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
	public function acceptInviteRequest($hashId, $slug, $requestId)
	{
		$post = Post::find(decodeHashId($hashId));

		if (!$post)
			echo 'that post does not exist.';

		if ($post->user->id !== Auth::id())
			echo 'You don\'t are not the author of this post.';

		$request = Requestt::find(decodeHashId($requestId));

		if ($request->state == RequestStates::PENDING)
			$request->state = RequestStates::ACCEPTED;
		else
			return redirect()->back();

		if ($request->save())
			return redirect('/post/' . $hashId . '/' . $slug . '/session');
		else
			echo 'Something went wrong, go back and try again.';
	}

	/**
	*
	* Decline an invite request
	*
	**/
	public function declineInviteRequest($hashId, $slug, $requestId)
	{
		$post = Post::find(decodeHashId($hashId));

		if (!$post)
			echo 'that post does not exist.';

		if ($post->user->id !== Auth::id())
			echo 'You don\'t are not the author of this post.';

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
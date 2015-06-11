<?php namespace App\Http\Controllers;

use Auth;
use App\Models\Post;
use App\Models\Requestt;
use App\Models\GameSession;
use App\Enums\RequestStates;
use App\Enums\GameSessionStates;

/**
* SessionController
*
* @uses     Controller
* @category Controllers
*/
class SessionController extends Controller
{
    /**
     * Accept an invite request
     *
     * @uses 	App\Models\Post
     * @uses 	decodeHashId()
     * @uses 	Auth
     * @uses 	App\Models\Requestt
     * @uses 	App\Enums\RequestStates
     * @uses 	redirect()
     *
     * @param 	string 		$hashId 	The hash id of the post associated with the request.
     * @param 	string 		$slug 		The slug of the post ^.
     * @param 	string 		$requestId  The hash id of the request.
     *
     * @return 	response 	Redirect to the gamesession page or return back or return error.
     */
	public function acceptInviteRequest($hashId, $slug, $requestId)
	{
		$post = Post::find(decodeHashId($hashId));

		if (!$post)
			echo 'that post does not exist.';

		if ($post->user->id !== Auth::id())
			echo 'You are not the author of this post.';

		$request = Requestt::find(decodeHashId($requestId));

		if ($request->state == RequestStates::PENDING)
			$request->state = RequestStates::ACCEPTED;
		else
			return redirect()->back();

		if ($request->save())
		{
			# notify requester

			return redirect('/post/' . $hashId . '/' . $slug . '/session');
		}
		else
		{
			echo 'Something went wrong, go back and try again.';
		}
	}


    /**
     * Decline an invite request
     *
     * @uses 	App\Models\Post
     * @uses 	decodeHashId()
     * @uses 	Auth
     * @uses 	App\Models\Requestt
     * @uses 	App\Enums\RequestStates
     * @uses 	redirect()
     *
     * @param 	string 		$hashId 	The hash id of the post associated with the request.
     * @param 	string 		$slug 		The slug of the post ^.
     * @param 	string 		$requestId  The hash id of the request.
     *
     * @return 	response 	Redirect to the gamesession page or return back or return error.
     */
	public function declineInviteRequest($hashId, $slug, $requestId)
	{
		$post = Post::find(decodeHashId($hashId));

		if (!$post)
			echo 'that post does not exist.';

		if ($post->user->id !== Auth::id())
			echo 'You are not the author of this post.';

		$request = Requestt::find(decodeHashId($requestId));

		if ($request->state == RequestStates::PENDING)
			$request->state = RequestStates::DECLINED;
		else
			return redirect()->back();

		if ($request->save())
		{
			# notify requester

			return redirect()->back();
		}
		else
		{
			echo 'Something went wrong, go back and try again.';
		}
	}

}
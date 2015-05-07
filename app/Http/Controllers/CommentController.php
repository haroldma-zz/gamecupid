<?php namespace App\Http\Controllers;

use Cache;
use App\Models\CommentsRenderer;
use Auth;
use Response;
use App\Models\CommentVote;
use App\Models\Comment;
use App\Enums\VoteStates;
use App\Enums\AjaxVoteResults;
use App\Models\Notification;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class CommentController extends Controller {

	/**
	*
	* Upvote an comment
	*
	**/
	public function upvote(Request $request)
	{
		if (!$request->ajax())
			return redirect('/');

		if (!Auth::check())
			return AjaxVoteResults::UNAUTHORIZED;

        $id = decodeHashId($request->get('id'));
		$comment = Comment::find($id);

		if (!$comment)
			return AjaxVoteResults::ERROR;

		if ($comment->user->id === Auth::user()->id)
			return 5;

		$check = Auth::user()->commentVotes()->where('comment_id', $id)->first();

        invalidateCache(generateAuthCacheKeyWithId("comment", "isUpvoted", $id));
        invalidateCache(generateAuthCacheKeyWithId("comment", "isDownvoted", $id));

		if ($check)
		{
			$vote = $check;

			if ($vote->state == VoteStates::UP)			// UNVOTED
			{
				$vote->delete();
				return AjaxVoteResults::UNVOTED;
			}
			else if ($vote->state == VoteStates::DOWN)	// UPVOTED FROM DOWNVOTE
			{
				$vote->state = VoteStates::UP;
				$vote->save();
				return AjaxVoteResults::VOTE_SWITCH;
			}
			else
				return AjaxVoteResults::ERROR;
		}
		else
		{
			if ($comment->castVote(VoteStates::UP))
				return AjaxVoteResults::NORMAL;
			else
				return AjaxVoteResults::ERROR;
		}
	}


	/**
	*
	* Downvote an comment
	*
	**/
	public function downvote(Request $request)
	{
		if (!$request->ajax())
			return redirect('/');

		if (!Auth::check())
			return AjaxVoteResults::UNAUTHORIZED;

		$id = decodeHashId($request->get('id'));
		$comment = Comment::find($id);

		if (!$comment)
			return AjaxVoteResults::ERROR;

		if ($comment->user->id === Auth::user()->id)
			return 5;

		$check = Auth::user()->commentVotes()->where('comment_id', $id)->first();

        invalidateCache(generateAuthCacheKeyWithId("comment", "isUpvoted", $id));
        invalidateCache(generateAuthCacheKeyWithId("comment", "isDownvoted", $id));

		if ($check)
		{
			$vote = $check;

			if ($vote->state == VoteStates::DOWN)			// UNVOTED
			{
				$vote->delete();
				return AjaxVoteResults::UNVOTED;
			}
			else if ($vote->state == VoteStates::UP)	// DOWNVOTED FROM UPVOTE
			{
				$vote->state = VoteStates::DOWN;
				$vote->save();
				return AjaxVoteResults::VOTE_SWITCH;
			}
			else
				return AjaxVoteResults::ERROR;
		}
		else
		{
			if ($comment->castVote(VoteStates::DOWN))
				return AjaxVoteResults::NORMAL;
			else
				return AjaxVoteResults::ERROR;
		}
	}

}
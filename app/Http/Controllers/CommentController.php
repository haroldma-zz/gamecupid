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

		$id = $request->get('id');
		$comment = Comment::find($id);

		if (!$comment)
			return AjaxVoteResults::ERROR;

		$check = Auth::user()->commentVotes()->where('comment_id', $id)->first();

		if ($check)
		{
			$vote = $check;

			if ($vote->state == VoteStates::UP)			// UNVOTED
			{
                // invalidate cache
                invalidateCache(generateAuthCacheKeyWithId("comment", "isUpvoted", $id));

				$vote->delete();
				return AjaxVoteResults::UNVOTED;
			}
			else if ($vote->state == VoteStates::DOWN)	// UPVOTED FROM DOWNVOTE
			{
                // invalidate cache
                invalidateCache(generateAuthCacheKeyWithId("comment", "isDownvoted", $id));

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

		$id = $request->get('id');
		$comment = Comment::find($id);

		if (!$comment)
			return AjaxVoteResults::ERROR;

		$check = Auth::user()->commentVotes()->where('comment_id', $id)->first();

		if ($check)
		{
			$vote = $check;

			if ($vote->state == VoteStates::DOWN)			// UNVOTED
			{
                // invalidate cache
                invalidateCache(generateAuthCacheKeyWithId("comment", "isDownvoted", $id));

				$vote->delete();
				return AjaxVoteResults::UNVOTED;
			}
			else if ($vote->state == VoteStates::UP)	// DOWNVOTED FROM UPVOTE
			{
                // invalidate cache
                invalidateCache(generateAuthCacheKeyWithId("comment", "isUpvoted", $id));

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
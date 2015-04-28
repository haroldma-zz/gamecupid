<?php namespace App\Http\Controllers;

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

		$check = Auth::user()->commentVotes()->where('comment_id', $id)->first();

		if ($check)
		{
			$comment = $check;

			if ($comment->state == VoteStates::UP)			// UNVOTED
			{
				$comment->delete();
				return AjaxVoteResults::UNVOTED;
			}
			else if ($comment->state == VoteStates::DOWN)	// UPVOTED FROM DOWNVOTE
			{
				$comment->state = VoteStates::UP;
				$comment->save();
				return AjaxVoteResults::VOTE_SWITCH;
			}
			else
			{
				return AjaxVoteResults::ERROR; 								// Error
			}
		}
		else
		{
			$comment            = new CommentVote;
			$comment->comment_id = $id;
			$comment->user_id   = Auth::user()->id;
			$comment->state     = VoteStates::UP;

			if ($comment->save())
			{
				return AjaxVoteResults::NORMAL;
			}
			else
			{
				return AjaxVoteResults::ERROR;
			}
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
			return 4;

		$id = $request->get('id');

		$check = Auth::user()->commentVotes()->where('comment_id', $id)->first();

		if ($check)
		{
			$comment = $check;

			if ($comment->state == VoteStates::DOWN)		// UNVOTED
			{
				$comment->delete();
				return AjaxVoteResults::UNVOTED;
			}
			else if ($comment->state == VoteStates::UP)	// DOWNVOTED FROM UPVOTE
			{
				$comment->state = VoteStates::DOWN;
				$comment->save();
				return AjaxVoteResults::VOTE_SWITCH;
			}
			else
			{
				return AjaxVoteResults::ERROR; 								// Error
			}
		}
		else
		{
			$comment            = new CommentVote;
			$comment->comment_id = $id;
			$comment->user_id   = Auth::user()->id;
			$comment->state     = VoteStates::DOWN;

			if ($comment->save())
			{
				return AjaxVoteResults::NORMAL;
			}
			else
			{
				return AjaxVoteResults::ERROR;
			}
		}
	}

}
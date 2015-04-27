<?php namespace App\Http\Controllers;

use Auth;
use Response;
use App\Models\Rep;
use App\Models\Invite;
use App\Models\InviteVote;
use App\Models\Comment;
use App\Models\RepEvent;
use App\Enums\RepEvents;
use App\Enums\VoteStates;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\InviteFormRequest;
use Cocur\Slugify\Slugify;
use App\Enums\AjaxVoteResults;
use Vinkla\Hashids\Facades\Hashids;

class InviteController extends Controller {

	/**
	*
	* New an invite
	*
	**/
	public function invite(InviteFormRequest $request)
	{
        $slugify = new Slugify();
        $slugify->addRule('+', 'plus');

		$invite                    = new Invite;
		$invite->title             = $request->get('title');
		$invite->slug              = $slugify->slugify($request->get('title'), "-");
		$invite->self_text         = $request->get('self_text');
		$invite->tag_text          = '-';
		$invite->player_count      = $request->get('player_count');
		$invite->requires_approval = ($request->get('requires_approval') == '' ? false : true);
		$invite->console_id        = $request->get('console_id');
		$invite->game_id           = $request->get('game_id');
		$invite->user_id           = Auth::user()->id;

		if ($invite->save())
		{
			$repEvent = RepEvent::find(RepEvents::CREATED_INVITE);

			$rep               = new Rep;
			$rep->rep_event_id = $repEvent->id;
			$rep->user_id      = Auth::user()->id;
			$rep->save();

			$not              = new Notification;
			$not->title       = "+{$repEvent->amount} rep";
			$not->description = $repEvent->event;
			$not->to_id       = Auth::user()->id;
			$not->save();

			return redirect('/');
		}
		else
		{
			return redirect()->back()->with('notice', ['error', 'Something went wrong... Try again.']);
		}
	}


	/**
	*
	* Upvote an invite
	*
	**/
	public function upvote(Request $request)
	{
		if (!$request->ajax())
			return redirect('/');

		if (!Auth::check())
			return AjaxVoteResults::UNAUTHORIZED;

		$id = $request->get('id');

		$check = Auth::user()->inviteVotes()->where('invite_id', $id)->first();

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
			{
				return AjaxVoteResults::ERROR; 								// Error
			}
		}
		else
		{
			$vote            = new InviteVote;
			$vote->invite_id = $id;
			$vote->user_id   = Auth::user()->id;
			$vote->state     = VoteStates::UP;

			if ($vote->save())
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
	* Downvote an invite
	*
	**/
	public function downvote(Request $request)
	{
		if (!$request->ajax())
			return redirect('/');

		if (!Auth::check())
			return 4;

		$id = $request->get('id');

		$check = Auth::user()->inviteVotes()->where('invite_id', $id)->first();

		if ($check)
		{
			$vote = $check;

			if ($vote->state == VoteStates::DOWN)		// UNVOTED
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
			{
				return AjaxVoteResults::ERROR; 								// Error
			}
		}
		else
		{
			$vote            = new InviteVote;
			$vote->invite_id = $id;
			$vote->user_id   = Auth::user()->id;
			$vote->state     = VoteStates::DOWN;

			if ($vote->save())
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
	* Comment on invite
	*
	**/
	public function comment($hashid, $slug, Request $request)
	{
		$invite = Invite::find(Hashids::decode($hashid));

		if (!$invite)
			return redirect()->back()->withInput()->with('notice', ['error', 'Invite not found.']);

		if ($request->get('self_text') == '')
			return redirect()->back()->withInput()->with('notice', ['error', 'You forgot to write a comment.']);

		if ($invite[0]->id !== $request->get('invite_id'))
			return redirect()->back()->withInput()->with('notice', ['error', 'Invalid action.']);

		$comment                = new Comment;
		$comment->self_text     = $request->get('self_text');
		$comment->markdown_text = '';
		$comment->deleted       = false;
		$comment->parent_id     = $request->get('parent_id');
		$comment->invite_id     = $invite[0]->id;
		$comment->user_id       = Auth::user()->id;

		if ($comment->save())
			return redirect()->back();

		return redirect()->back()->withInput()->with('notice', ['error', 'Something went wrong, try again.']);
	}

}














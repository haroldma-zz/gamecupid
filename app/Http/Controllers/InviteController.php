<?php namespace App\Http\Controllers;

use Auth;
use Response;
use App\Models\Rep;
use App\Models\Invite;
use App\Models\Comment;
use App\Models\RepEvent;
use App\Models\Parsedown;
use App\Enums\RepEvents;
use App\Enums\VoteStates;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Requests\InviteFormRequest;
use Cocur\Slugify\Slugify;
use App\Enums\AjaxVoteResults;

class InviteController extends Controller {

	/**
	*
	* New an invite
	*
	**/
	public function invite(InviteFormRequest $request)
	{
		$parsedown = new Parsedown();
		$slugify   = new Slugify();
        $slugify->addRule('+', 'plus');

		$invite                    = new Invite;
		$invite->title             = $request->get('title');
		$invite->slug              = $slugify->slugify($request->get('title'), "-");
		$invite->self_text         = $parsedown->text($request->get('self_text'));
		$invite->markdown_text     = $request->get('self_text');
		$invite->tag_text          = '-';
		$invite->player_count      = $request->get('player_count');
		$invite->requires_approval = ($request->get('requires_approval') == '' ? false : true);
		$invite->console_id        = $request->get('console_id');
		$invite->game_id           = $request->get('game_id');
		$invite->user_id           = Auth::user()->id;

		if ($invite->save())
		{
            $invite->castVote(VoteStates::UP);
            
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

		$id = decodeHashId($request->get('id'));
        $invite = Invite::find($id);

        if (!$invite)
            return AjaxVoteResults::ERROR;

		$check = Auth::user()->inviteVotes()->where('invite_id', $id)->first();

        invalidateCache(generateAuthCacheKeyWithId("invite", "isUpvoted", $id));
        invalidateCache(generateAuthCacheKeyWithId("invite", "isDownvoted", $id));

		if ($check)
		{
			$vote = $check;

			if ($vote->state == VoteStates::UP)			// UNVOTED
			{
                // invalidate cache
                invalidateCache(generateAuthCacheKeyWithId("invite", "isUpvoted", $id));

				$vote->delete();
				return AjaxVoteResults::UNVOTED;
			}
			else if ($vote->state == VoteStates::DOWN)	// UPVOTED FROM DOWNVOTE
			{
                // invalidate cache
                invalidateCache(generateAuthCacheKeyWithId("invite", "isDownvoted", $id));

				$vote->state = VoteStates::UP;
				$vote->save();
				return AjaxVoteResults::VOTE_SWITCH;
			}
			else
				return AjaxVoteResults::ERROR;
		}
		else
		{
			if ($invite->castVote(VoteStates::UP))
				return AjaxVoteResults::NORMAL;
			else
				return AjaxVoteResults::ERROR;
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
			return AjaxVoteResults::UNAUTHORIZED;

		$id = decodeHashId($request->get('id'));
        $invite = Invite::find($id);

        if (!$invite)
            return AjaxVoteResults::ERROR;

		$check = Auth::user()->inviteVotes()->where('invite_id', $id)->first();

        invalidateCache(generateAuthCacheKeyWithId("invite", "isUpvoted", $id));
        invalidateCache(generateAuthCacheKeyWithId("invite", "isDownvoted", $id));

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
				return AjaxVoteResults::ERROR;
		}
		else
		{
            if ($invite->castVote(VoteStates::DOWN))
                return AjaxVoteResults::NORMAL;
            else
                return AjaxVoteResults::ERROR;
            }

        }


	/**
	*
	* Comment on invite
	*
	**/
	public function comment($hashid, $slug, Request $request)
	{
        $id = decodeHashId($hashid);
		$invite = Invite::find($id);

		if (!$invite)
			return redirect()->back()->withInput()->with('notice', ['error', 'Invite not found.']);

		if ($request->get('self_text') == '')
			return redirect()->back()->withInput()->with('notice', ['error', 'You forgot to write a comment.']);

		if ($invite->id != $request->get('invite_id'))
			return redirect()->back()->withInput()->with('notice', ['error', 'Invalid action.']);

		$parsedown              = new Parsedown();
		$comment                = new Comment;
		$comment->self_text     = $parsedown->text($request->get('self_text'));
		$comment->markdown_text = $request->get('self_text');
		$comment->deleted       = false;
		$comment->parent_id     = $request->get('parent_id');
		$comment->invite_id     = $invite->id;
		$comment->user_id       = Auth::user()->id;

		if ($comment->save()) {
            $comment->castVote(VoteStates::UP);
            return redirect()->back();
        }

		return redirect()->back()->withInput()->with('notice', ['error', 'Something went wrong, try again.']);
	}

}














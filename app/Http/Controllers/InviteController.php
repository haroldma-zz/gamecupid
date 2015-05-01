<?php namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\Game;
use Auth;
use Response;
use App\Models\Invite;
use App\Models\Comment;
use App\Models\Parsedown;
use App\Enums\RepEvents;
use App\Enums\VoteStates;
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

        $user = Auth::user();

        if ($user->rep(false) <= 0)
            return redirect()->back()->with('notice', ['error', 'Not enough rep.']);

        $console = Console::find($request->get('console_id'));
        if (!$console)
            return redirect()->back()->with('notice', ['error', 'Console doesn\'t exists']);

        // Check if the player has a verified profile for the platform
        if ($user->profiles->where('platform_id', $console->platform_id)->first() == null)
            return redirect()->back()
                ->with('notice', ['error', "You need a verified {$console->platform->name} profile to post invites for $console->name."]);

        // Make sure the game id is for an existing game and the console id matches
        $game = Game::find($request->get('game_id'));
        if (!$game)
            return redirect()->back()->with('notice', ['error', 'Game doesn\'t exists']);

        // Verified the game is supported on the selected console
        if ($game->consoles->where('console_id', $console->id)->first() == null)
            return redirect()->back()->with('notice', ['error', 'This game is not supported for the selected console.']);

		$invite                    = new Invite;
		$invite->title             = $request->get('title');
		$invite->slug              = $slugify->slugify($request->get('title'), "-");
		$invite->self_text         = $parsedown->text($request->get('self_text'));
		$invite->markdown_text     = $request->get('self_text');
		//$invite->tag_text          = '-'; TODO
		$invite->player_count      = max($request->get('player_count'), 1);
		$invite->console_id        = $console->id;
		$invite->game_id           = $game->id;
		$invite->user_id           = $user->id;

		if ($invite->save())
		{
            $invite->castVote(VoteStates::UP);
            giveRepAndNotified(RepEvents::CREATED_INVITE);
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
		$id       = decodeHashId($hashid);
		$parentId = decodeHashId($request->get('parent_id'));

        if ($parentId == 0)
		    $invite = Invite::find($id);
        else {
            $parent = Comment::find($parentId);

            if (!$parent || $parent->invite_id != $id)
            {
	            if ($request->ajax())
	            	return ['0']; 			# Invalid invite id

                return redirect()->back()->withInput()->with('notice', ['error', 'Invalid invite id.']);
            }
        }

		if (($parentId != 0 && !$parent) || ($parentId == 0 && !$invite))
		{
            if ($request->ajax())
            	return ['1']; 			# Invite not found

			return redirect()->back()->withInput()->with('notice', ['error', 'Invite not found.']);
		}

		if ($request->get('self_text') == '')
		{
            if ($request->ajax())
            	return ['2']; 			# No self_text

			return redirect()->back()->withInput()->with('notice', ['error', 'You forgot to write a comment.']);
		}

		$parsedown              = new Parsedown();
		$comment                = new Comment;
		$comment->self_text     = $parsedown->text($request->get('self_text'));
		$comment->markdown_text = $request->get('self_text');
		$comment->deleted       = false;
		$comment->parent_id     = $parentId;
		$comment->invite_id     = $id;
		$comment->user_id       = Auth::id();

		if ($comment->save()) {
            $comment->castVote(VoteStates::UP);

            if ($parentId != 0 && $parent->user_id != $comment->user_id)
                notifiedAboutComment($comment->id, $parent->user_id);

            if ($request->ajax())
            	return ['3', ['created_at' => $comment->created_at, 'hashId' => hashId($comment->id), 'self_text' => $comment->self_text, 'permalink' => $comment->invite()->getPermalink()]]; 			# comment success

            return redirect()->back();
        }

		return redirect()->back()->withInput()->with('notice', ['error', 'Something went wrong, try again.']);
	}

}














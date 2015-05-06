<?php namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\Game;
use Auth;
use Response;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Parsedown;
use App\Enums\RepEvents;
use App\Enums\VoteStates;
use Illuminate\Http\Request;
use App\Http\Requests\PostFormRequest;
use Cocur\Slugify\Slugify;
use App\Enums\AjaxVoteResults;

class PostController extends Controller {

	/**
	*
	* New an post
	*
	**/
	public function post(PostFormRequest $request)
	{
		if (!$request->ajax())
			return redirect('/');

		# title should have at least 1 alphabetic character
		if (!preg_match('/[a-z]+/i', $request->get('title')))
			return Response::make('The title should have at least 1 alphabetic character.', 500);

		$parsedown = new Parsedown();
		$slugify   = new Slugify();
        $slugify->addRule('+', 'plus');

        $user = Auth::user();

        if ($user->rep(false) <= 0)
            return Response::make('Not enough rep.', 500);

        $console = Console::find(decodeHashId($request->get('console_id')));
        if (!$console)
            return Response::make('Console doesn\'t exists', 500);

        // Check if the player has a verified profile for the platform
        if ($user->profiles->where('platform_id', $console->platform_id)->first() == null)
            return Response::make("You need a verified {$console->platform->name} profile to post posts for $console->name.", 500);

        // Make sure the game id is for an existing game and the console id matches
        $game = Game::find(decodeHashId($request->get('game_id')));
        if (!$game)
            return Response::make('Game doesn\'t exists', 500);

        // Verified the game is supported on the selected console
        if ($game->consoles->where('console_id', $console->id)->first() == null)
            return Response::make('This game is not supported for the selected console.', 500);

		$post                    = new Post;
		$post->title             = $request->get('title');
		$post->slug              = $slugify->slugify($request->get('title'), "-");
		$post->self_text         = $parsedown->text($request->get('self_text'));
		$post->markdown_text     = $request->get('self_text');
		//$post->tag_text          = '-'; TODO
		$post->max_players       = max((int)$request->get('max_players'), 1);
		$post->verified_only     = ($request->get('verified') == 'yes' ? true : false);
		$post->console_id        = $console->id;
		$post->game_id           = $game->id;
		$post->user_id           = $user->id;

		if ($post->save())
		{
            $post->castVote(VoteStates::UP);
            giveRepAndNotified(RepEvents::CREATED_POST);

			return Response::make('success', 200);
		}
		else
		{
			return Response::make('failed', 500);
		}
	}


	/**
	*
	* Upvote an post
	*
	**/
	public function upvote(Request $request)
	{
		if (!$request->ajax())
			return redirect('/');

		if (!Auth::check())
			return AjaxVoteResults::UNAUTHORIZED;

		$id = decodeHashId($request->get('id'));
        $post = Post::find($id);

        if (!$post)
            return AjaxVoteResults::ERROR;

		$check = Auth::user()->postVotes()->where('post_id', $id)->first();

        invalidateCache(generateAuthCacheKeyWithId("post", "isUpvoted", $id));
        invalidateCache(generateAuthCacheKeyWithId("post", "isDownvoted", $id));

		if ($check)
		{
			$vote = $check;

			if ($vote->state == VoteStates::UP)			// UNVOTED
			{
                // invalidate cache
                invalidateCache(generateAuthCacheKeyWithId("post", "isUpvoted", $id));

				$vote->delete();
				return AjaxVoteResults::UNVOTED;
			}
			else if ($vote->state == VoteStates::DOWN)	// UPVOTED FROM DOWNVOTE
			{
                // invalidate cache
                invalidateCache(generateAuthCacheKeyWithId("post", "isDownvoted", $id));

				$vote->state = VoteStates::UP;
				$vote->save();
				return AjaxVoteResults::VOTE_SWITCH;
			}
			else
				return AjaxVoteResults::ERROR;
		}
		else
		{
			if ($post->castVote(VoteStates::UP))
				return AjaxVoteResults::NORMAL;
			else
				return AjaxVoteResults::ERROR;
		}
	}


	/**
	*
	* Downvote an post
	*
	**/
	public function downvote(Request $request)
	{
		if (!$request->ajax())
			return redirect('/');

		if (!Auth::check())
			return AjaxVoteResults::UNAUTHORIZED;

		$id = decodeHashId($request->get('id'));
        $post = Post::find($id);

        if (!$post)
            return AjaxVoteResults::ERROR;

		$check = Auth::user()->postVotes()->where('post_id', $id)->first();

        invalidateCache(generateAuthCacheKeyWithId("post", "isUpvoted", $id));
        invalidateCache(generateAuthCacheKeyWithId("post", "isDownvoted", $id));

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
            if ($post->castVote(VoteStates::DOWN))
                return AjaxVoteResults::NORMAL;
            else
                return AjaxVoteResults::ERROR;
            }

        }


	/**
	*
	* Comment on post
	*
	**/
	public function comment($hashid, $slug, Request $request)
	{
		$id       = decodeHashId($hashid);
		$parentId = decodeHashId($request->get('parent_id'));

        if ($parentId == 0)
		    $post = Post::find($id);
        else {
            $parent = Comment::find($parentId);

            if (!$parent || $parent->post_id != $id)
            {
	            if ($request->ajax())
	            	return ['0']; 			# Invalid post id

                return redirect()->back()->withInput()->with('notice', ['error', 'Invalid post id.']);
            }
        }

		if (($parentId != 0 && !$parent) || ($parentId == 0 && !$post))
		{
            if ($request->ajax())
            	return ['1']; 			# post not found

			return redirect()->back()->withInput()->with('notice', ['error', 'post not found.']);
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
		$comment->post_id       = $id;
		$comment->user_id       = Auth::id();

		if ($comment->save()) {
            $comment->castVote(VoteStates::UP);

            if ($parentId != 0 && $parent->user_id != $comment->user_id)
                notifiedAboutCommentReply($comment->id, $parent->user_id);

            if ($parentId == 0 && $post->user_id != $comment->user_id)
            	notifiedAboutComment($post->id, $post->user_id);

            if ($request->ajax())
            	return ['3', ['created_at' => $comment->created_at, 'hashId' => hashId($comment->id), 'self_text' => $comment->self_text, 'permalink' => $comment->post()->getPermalink()]]; 			# comment success

            return redirect()->back();
        }

		return redirect()->back()->withInput()->with('notice', ['error', 'Something went wrong, try again.']);
	}

}














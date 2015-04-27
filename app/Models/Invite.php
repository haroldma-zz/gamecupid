<?php namespace App\Models;

use Auth;
use App\Models\CommentsRenderer;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VoteStates;
use Vinkla\Hashids\Facades\Hashids;

class Invite extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invites';

	public function upvotes()
    {
        return $this->votes()->where('state', VoteStates::UP)->get();
    }

    public function upvoteCount()
    {
        return $this->votes()->where('state', VoteStates::UP)->count();
    }

    public function downvotes()
    {
        return $this->votes()->where('state', VoteStates::DOWN)->get();
    }

    public function downvoteCount()
    {
        return $this->votes()->where('state', VoteStates::DOWN)->count();
    }

    public function isUpvoted()
    {
        return Auth::user()->inviteVotes()->where('invite_id', $this->id)->where('state', VoteStates::UP)
            ->first() != null;
    }

    public function isDownvoted()
    {
        return Auth::user()->inviteVotes()->where('invite_id', $this->id)->where('state', VoteStates::DOWN)
            ->first() != null;
    }

    public function hashid()
    {
    	return Hashids::encode($this->id);
    }

    public function renderComments()
    {
        $commentsids = [];

        foreach ($this->comments as $c)
        {
            $commentsids[] = $c->id;
        }

        $commentlist = new CommentsRenderer($commentsids);

        return $commentlist->print_comments();
    }

	/**
	*
	* Relations
	*
	**/
	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

	public function game()
	{
		return $this->belongsTo('App\Models\Game', 'game_id', 'id');
	}

	public function console()
	{
		return $this->belongsTo('App\Models\Console', 'console_id', 'id');
	}

	public function platform()
	{
		return $this->belongsTo('App\Models\Platform', 'platform_id', 'id');
	}

	public function accepts()
	{
		return $this->hasMany('App\Models\Accept', 'invite_id', 'id');
	}

    public function votes()
    {
        return $this->hasMany('App\Models\InviteVote', 'invite_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'invite_id', 'id');
    }

}
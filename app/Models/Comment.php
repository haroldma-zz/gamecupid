<?php namespace App\Models;

use Auth;
use App\Enums\VoteStates;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';


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
        if (!Auth::check())
            return false;

        return Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::UP)
            ->first() != null;
    }

    public function isDownvoted()
    {
        if (!Auth::check())
            return false;

        return Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::DOWN)
            ->first() != null;
    }


	/**
	*
	* Relations
	*
	**/
	public function user()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}

	public function invite()
	{
		return $this->hasOne('App\Models\Invite', 'id', 'invite_id');
	}

	public function children()
	{
		return $this->hasMany('App\Models\Comment', 'parent_id', 'id');
	}

	public function parent()
	{
		return $this->hasOne('App\Models\Comment', 'id', 'parent_id');
	}

    public function votes()
    {
        return $this->hasMany('App\Models\CommentVote', 'comment_id', 'id');
    }

}

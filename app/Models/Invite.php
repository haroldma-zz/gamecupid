<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invites';

	public function upvotes()
    {
        return $this->votes()->where('state', 1)->get();
    }

    public function upvoteCount()
    {
        return $this->votes()->where('state', 1)->count();
    }

    public function downvotes()
    {
        return $this->votes()->where('state', 0)->get();
    }

    public function downvoteCount()
    {
        return $this->votes()->where('state', 0)->count();
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

}
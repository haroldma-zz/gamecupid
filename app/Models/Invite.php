<?php namespace App\Models;

use Auth;
use Carbon\Carbon;
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
        if (!Auth::check())
            return false;

        return Auth::user()->inviteVotes()->where('invite_id', $this->id)->where('state', VoteStates::UP)
            ->first() != null;
    }

    public function isDownvoted()
    {
        if (!Auth::check())
            return false;

        return Auth::user()->inviteVotes()->where('invite_id', $this->id)->where('state', VoteStates::DOWN)
            ->first() != null;
    }

    public function hashid()
    {
    	return Hashids::encode($this->id);
    }

    public function renderComments($sort)
    {
        $commentlist = new CommentsRenderer($this->sortParentComments($sort, 1), $sort);

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

    public function sortParentComments($sort, $page)
    {
        $pageSize = 10;

        if (!is_int($page))
            $page = 1;

        $page    = ($page - 1) * $pageSize;
        $pageEnd = $pageSize;

        $time = array(
            Carbon::now()->subDay(),
            Carbon::now()
        );

        $sqlFunction = "calculateHotness(getCommentUpvotes(id), getCommentDownvotes(id), created_at)";

        if ($sort == "controversial")
            $sqlFunction = "calculateControversy(getCommentUpvotes(id), getCommentDownvotes(id))";
        else if ($sort == "best")
            $sqlFunction = "calculateBest(getCommentUpvotes(id), getCommentDownvotes(id))";

        $query = "SELECT *, $sqlFunction as sort FROM comments
                  WHERE created_at BETWEEN '$time[0]' and '$time[1]'
                  AND invite_id = $this->id AND parent_id = 0
                  ORDER BY sort DESC LIMIT $page, $pageEnd;";

        if ($sort == "new")
            $query = "SELECT * FROM comments
                  WHERE created_at BETWEEN '$time[0]' and '$time[1]'
                  AND invite_id = $this->id AND parent_id = 0
                  ORDER BY created_at DESC LIMIT $page, $pageEnd;";
        else if ($sort == "top")
            $query = "SELECT *, getCommentUpvotes(id) as upvotes, getCommentDownvotes(id) as downvotes FROM comments
                  WHERE created_at BETWEEN '$time[0]' and '$time[1]'
                  AND invite_id = $this->id AND parent_id = 0
                  ORDER BY upvotes - downvotes DESC LIMIT $page, $pageEnd;";

         return Comment::hydrateRaw($query);
    }

}
<?php namespace App\Models;

use Auth;
use App\Enums\VoteStates;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comment extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';

    public function castVote($state)
    {
        $vote            = new CommentVote;
        $vote->comment_id = $this->id;
        $vote->user_id   = Auth::user()->id;
        $vote->state     = $state;
        return $vote->save();
    }

    public function totalVotes()
    {
        return $this->upvoteCount() - $this->downvoteCount();
    }

	public function upvotes()
    {
        return $this->votes()->where('state', VoteStates::UP)->get();
    }

    private $_commentCount = -1;
    public function childCount()
    {
        if ($this->_commentCount != -1)
            return $this->_commentCount;

        $this->_commentCount = $this->children()->count();
        return $this->_commentCount;
    }

    private $_upvoteCount = -1;
    public function upvoteCount()
    {
        if ($this->_upvoteCount != -1)
            return $this->_upvoteCount;
        $this->_upvoteCount = $this->votes()->where('state', VoteStates::UP)->count();
        return $this->_upvoteCount;
    }

    public function downvotes()
    {
        return $this->votes()->where('state', VoteStates::DOWN)->get();
    }

    private $_downvoteCount = -1;
    public function downvoteCount()
    {
        if ($this->_downvoteCount != -1)
            return $this->_downvoteCount;
        $this->_downvoteCount = $this->votes()->where('state', VoteStates::DOWN)->count();
        return $this->_downvoteCount;
    }

    private $_isUpvoted = null;
    public function isUpvoted()
    {
        if (!Auth::check())
            return false;

        if ($this->_isUpvoted != null)
            return $this->_isUpvoted;

        $this->_isUpvoted = Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::UP)
                ->first() != null;
        return $this->_isUpvoted;
    }

    private $_isDownvoted = null;
    public function isDownvoted()
    {
        if (!Auth::check())
            return false;

        if ($this->_isDownvoted != null)
            return $this->_isDownvoted;

        $this->_isDownvoted = Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::DOWN)
                ->first() != null;
        return $this->_isDownvoted;
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

    public function sortChildComments($sort, $page, $limit)
    {
        $pageSize = $limit;

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
                  AND parent_id = $this->id
                  ORDER BY sort DESC LIMIT $page, $pageEnd;";

        if ($sort == "new")
            $query = "SELECT * FROM comments
                  WHERE created_at BETWEEN '$time[0]' and '$time[1]'
                  AND parent_id = $this->id
                  ORDER BY created_at DESC LIMIT $page, $pageEnd;";
        else if ($sort == "top")
            $query = "SELECT *, getCommentUpvotes(id) as upvotes, getCommentDownvotes(id) as downvotes FROM comments
                  WHERE created_at BETWEEN '$time[0]' and '$time[1]'
                  AND parent_id = $this->id
                  ORDER BY upvotes - downvotes DESC LIMIT $page, $pageEnd;";

        return Comment::hydrateRaw($query);
    }

}

<?php namespace App\Models;

use Auth;
use App\Enums\VoteStates;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;

class Comment extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';


    /**
    *
    * Vars
    *
    **/
    private $_commentCount  = -1;
    private $_upvoteCount   = -1;
    private $_downvoteCount = -1;
    private $_isUpvoted     = null;
    private $_isDownvoted   = null;


    public function hashid()
    {
        return Hashids::encode($this->id);
    }

    public function castVote($state)
    {
        $vote             = new CommentVote;
        $vote->comment_id = $this->id;
        $vote->user_id    = Auth::user()->id;
        $vote->state      = $state;
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

    public function childCount()
    {
        if ($this->_commentCount != -1)
            return $this->_commentCount;

        $key   = generateCacheKeyWithId("comment", "childrenCount", $this->id);
        $cache = getCache($key);

        if ($cache != null) {
            $this->_commentCount = $cache;
            return $this->_commentCount;
        }

        $this->_commentCount = $this->children()->count();
        return setCacheCount($key, $this->_commentCount);
    }

    public function upvoteCount()
    {
        if ($this->_upvoteCount != -1)
            return $this->_upvoteCount;

        $key   = generateCacheKeyWithId("comment", "upvoteCount", $this->id);
        if (hasCache($key, $cache)) {
            $this->_upvoteCount = $cache;
            return $this->_upvoteCount;
        }

        $this->_upvoteCount = $this->votes()->where('state', VoteStates::UP)->count();
        return setCacheCount($key, $this->_upvoteCount);
    }

    public function downvotes()
    {
        return $this->votes()->where('state', VoteStates::DOWN)->get();
    }

    public function downvoteCount()
    {
        if ($this->_downvoteCount != -1)
            return $this->_downvoteCount;

        $key   = generateCacheKeyWithId("comment", "downvoteCount", $this->id);
        if (hasCache($key, $cache)) {
            $this->_downvoteCount = $cache;
            return $this->_downvoteCount;
        }

        $this->_downvoteCount = $this->votes()->where('state', VoteStates::DOWN)->count();
        return setCacheCount($key, $this->_downvoteCount);
    }

    public function isUpvoted()
    {
        if (!Auth::check())
            return false;

        $key   = generateAuthCacheKeyWithId("comment", "isUpvoted", $this->id);

        if (hasCache($key, $cache)) {
            return $cache;
        }

        $check = Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::UP)
                            ->first() != null;

        return setCache($key, $check, Carbon::now()->addDay());
    }

    public function isDownvoted()
    {
        if (!Auth::check())
            return false;

        $key   = generateAuthCacheKeyWithId("comment", "isDownvoted", $this->id);

        if (hasCache($key, $cache)) {
            return $cache;
        }

        $check = Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::DOWN)
                            ->first() != null;

        return setCache($key, $check, Carbon::now()->addDay());
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

    public function sortChildComments($sort, $page, $limit, $cacheExpire)
    {
        $pageSize = $limit;

        if (!is_int($page))
            $page = 1;

        $key = generateCacheKeyWithId("invite", "comment-parents-$sort-p-$page-l-$limit-t-day", $this->id);
        if ($cacheExpire != 0) {
            if (hasCache($key, $cache))
                return $cache;
        }

        $page    = ($page - 1) * $pageSize;
        $pageEnd = $pageSize;

        $time = array(
            Carbon::now()->subDay(),
            Carbon::now()
        );

        $sqlFunction = "calculateBest(getCommentUpvotes(id), getCommentDownvotes(id))";

        if ($sort == "controversial")
            $sqlFunction = "calculateControversy(getCommentUpvotes(id), getCommentDownvotes(id))";
        else if ($sort == "hot")
            $sqlFunction = "calculateHotness(getCommentUpvotes(id), getCommentDownvotes(id), created_at)";

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

        $hydrated = Comment::hydrateRaw($query);
        if ($cacheExpire == 0)
            return $hydrated;
        return setCacheWithSeconds($key, $hydrated, $cacheExpire);
    }

}

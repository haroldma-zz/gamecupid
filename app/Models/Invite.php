<?php namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VoteStates;

class Invite extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invites';


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
    private $_cacheGame     = null;


    /**
    *
    * Custom functions
    *
    **/
    public function getPermalink()
    {
        return '/invite/' . hashId($this->id) . '/' . $this->slug . '/';
    }

    public function castVote($state)
    {
        $vote            = new InviteVote;
        $vote->invite_id = $this->id;
        $vote->user_id   = Auth::user()->id;
        $vote->state     = $state;
        return $vote->save();
    }

    public function totalVotes()
    {
        return $this->upvoteCount() - $this->downvoteCount();
    }

    public function commentCount()
    {
        if ($this->_commentCount != -1)
            return $this->_commentCount;

        $key   = generateCacheKeyWithId("invite", "commentCount", $this->id);
        if (hasCache($key, $cache)) {
            $this->_commentCount = $cache;
            return $this->_commentCount;
        }

        $this->_commentCount = $this->comments()->count();
        return setCacheCount($key, $this->_commentCount);
    }

	public function upvotes()
    {
        return $this->votes()->where('state', VoteStates::UP)->get();
    }

    public function upvoteCount()
    {
        if ($this->_upvoteCount != -1)
            return $this->_upvoteCount;

        $key   = generateCacheKeyWithId("invite", "upvoteCount", $this->id);
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

        $key   = generateCacheKeyWithId("invite", "downvoteCount", $this->id);
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

        if ($this->_isUpvoted != null)
            return $this->_isUpvoted;

        $key   = generateAuthCacheKeyWithId("invite", "isUpvoted", $this->id);
        if (hasCache($key, $cache)) {
            $this->_isUpvoted = $cache;
            return $cache;
        }

        $this->_isUpvoted = Auth::user()->inviteVotes()->where('invite_id', $this->id)->where('state', VoteStates::UP)
                            ->first() != null;

        return setCache($key, $this->_isUpvoted, Carbon::now()->addDay());
    }

    public function isDownvoted()
    {
        if (!Auth::check())
            return false;

        $key   = generateAuthCacheKeyWithId("invite", "isDownvoted", $this->id);
        if (hasCache($key, $cache)) {
            $this->_isDownvoted = $cache;
            return $cache;
        }

        $this->_isDownvoted = Auth::user()->inviteVotes()->where('invite_id', $this->id)->where('state', VoteStates::DOWN)
                              ->first() != null;
        return setCache($key, $this->_isDownvoted, Carbon::now()->addDay());
    }

    public function renderComments($sort)
    {
        if (empty($sort))
            $sort = "best";

        $count = $this->commentCount();
        $expire = 20;

        if ($count < 10)
            $expire = 0;
        else if ($count < 50)
            $expire = 5;
        else if ($count < 100)
            $expire = 10;
        else if ($count < 500)
            $expire = 15;

        $commentlist = new CommentsRenderer($this, $sort, $expire);
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
        if ($this->_cacheGame != null)
            return $this->_cacheGame;

        $key   = generateCacheKeyWithId("model", "game", $this->game_id);
        if (hasCache($key, $cache)) {
            $this->_cacheGame = $cache;
            return $this->_cacheGame;
        }

        $game = $this->belongsTo('App\Models\Game', 'game_id', 'id')->first();

        setCache($key, $game, Carbon::now()->addDay());
        $this->_cacheGame = $game;
        return $game;
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

    public function sortParentComments($sort, $page, $limit, $cacheExpire)
    {
        if (!is_int($page))
            $page = 1;

        $key = generateCacheKeyWithId("invite", "comment-parents-$sort-p-$page-l-$limit-t-day", $this->id);
        if ($cacheExpire != 0) {
            if (hasCache($key, $cache))
                return $cache;
        }

        $page    = ($page - 1) * $limit;
        $pageEnd = $limit;

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

        $hydrated = Comment::hydrateRaw($query);
        if ($cacheExpire == 0)
            return $hydrated;
        return setCacheWithSeconds($key, $hydrated, $cacheExpire);
    }

}
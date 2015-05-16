<?php namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Enums\VoteStates;

class Post extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';


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
    * Scopes
    *
    **/
    public function scopePsn($query)
    {
        return $query->where('console_id', '=', 3)->where('console_id', '=', 4, 'OR');
    }

    public function scopeXbl($query)
    {
        return $query->where('console_id', '=', 1)->where('console_id', '=', 2, 'OR');
    }

    public function scopeSteam($query)
    {
        return $query->where('console_id', '=', 5);
    }


    /**
    *
    * Custom functions
    *
    **/
    public function getPermalink()
    {
        return '/post/' . hashId($this->id) . '/' . $this->slug . '/';
    }

    public function castVote($state)
    {
        $vote            = new PostVote;
        $vote->post_id   = $this->id;
        $vote->user_id   = Auth::id();
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

        $key   = generateCacheKeyWithId("post", "commentCount", $this->id);
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

        $key   = generateCacheKeyWithId("post", "upvoteCount", $this->id);
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

        $key   = generateCacheKeyWithId("post", "downvoteCount", $this->id);
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

        $key   = generateAuthCacheKeyWithId("post", "isUpvoted", $this->id);
        if (hasCache($key, $cache)) {
            $this->_isUpvoted = $cache;
            return $cache;
        }

        $this->_isUpvoted = Auth::user()->postVotes()->where('post_id', $this->id)->where('state', VoteStates::UP)
                            ->first() != null;

        return setCache($key, $this->_isUpvoted, Carbon::now()->addDay());
    }

    public function isDownvoted()
    {
        if (!Auth::check())
            return false;

        $key   = generateAuthCacheKeyWithId("post", "isDownvoted", $this->id);
        if (hasCache($key, $cache)) {
            $this->_isDownvoted = $cache;
            return $cache;
        }

        $this->_isDownvoted = Auth::user()->postVotes()->where('post_id', $this->id)->where('state', VoteStates::DOWN)
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

        $commentlist = new CommentsRenderer;
        $commentlist->prepareForpost($this, $sort, $expire);
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

        $this->_cacheGame = $this->belongsTo('App\Models\Game', 'game_id', 'id')->first();
        return setCache($key, $this->_cacheGame, Carbon::now()->addDay());;
	}

    private $_console;
	public function console()
	{
        if ($this->_console != null)
            return $this->_console;

        $key   = generateCacheKeyWithId("model", "console", $this->console_id);
        if (hasCache($key, $cache)) {
            $this->_console = $cache;
            return $this->_console;
        }

        $this->_console = $this->belongsTo('App\Models\Console', 'console_id', 'id')->first();
        return setCache($key, $this->_console, Carbon::now()->addDay());;
	}

	public function platform()
	{
		return $this->belongsTo('App\Models\Platform', 'platform_id', 'id');
	}

	public function accepts()
	{
		return $this->hasMany('App\Models\Accept', 'post_id', 'id');
	}

    public function votes()
    {
        return $this->hasMany('App\Models\PostVote', 'post_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'post_id', 'id');
    }

    public function sortParentComments($sort, $page, $limit, $cacheExpire)
    {
        $key = generateCacheKeyWithId("post", "comment-parents-$sort-p-$page-l-$limit", $this->id);
        if ($cacheExpire != 0) {
            if (hasCache($key, $cache))
                return $cache;
        }

        $query = "GetBestComments($this->id, 0, 0, 10)";

        if ($sort == "controversial")
            $query = "GetControversialComments($this->id, 0, 0, 10)";
        else if ($sort == "hot")
            $query = "GetHotComments($this->id, 0, 0, 10)";
        else if ($sort == "new")
            $query = "GetNewComments($this->id, 0, 0, 10)";
        else if ($sort == "top")
            $query = "GetTopComments($this->id, 0, 0, 10)";

        $hydrated = Comment::hydrateRaw('CALL ' . $query);
        if ($cacheExpire == 0)
            return $hydrated;
        return setCacheWithSeconds($key, $hydrated, $cacheExpire);
    }

}
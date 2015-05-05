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
    private $_post          = null;


    public function getPermalink()
    {
        return $this->post()->getPermalink() . hashId($this->id);
    }

    public function castVote($state)
    {
        $vote             = new CommentVote;
        $vote->comment_id = $this->id;
        $vote->user_id    = Auth::id();
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

        if ($this->_isUpvoted != null)
            return $this->_isUpvoted;

        $key   = generateAuthCacheKeyWithId("comment", "isUpvoted", $this->id);

        if (hasCache($key, $cache)) {
            $this->_isUpvoted = $cache;
            return $cache;
        }

        $this->_isUpvoted = Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::UP)
                            ->first() != null;

        return setCache($key, $this->_isUpvoted, Carbon::now()->addDay());
    }

    public function isDownvoted()
    {
        if (!Auth::check())
            return false;

        if ($this->_isDownvoted != null)
            return $this->_isDownvoted;

        $key   = generateAuthCacheKeyWithId("comment", "isDownvoted", $this->id);

        if (hasCache($key, $cache)) {
            $this->_isDownvoted = $cache;
            return $cache;
        }

        $this->_isDownvoted = Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::DOWN)
                            ->first() != null;

        return setCache($key, $this->_isDownvoted, Carbon::now()->addDay());
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

	public function post()
	{
        if ($this->_post != null)
            return $this->_post;

        $key = generateCacheKeyWithId("model", "post", $this->post_id);
        if (hasCache($key, $cache)) {
            $this->_post = $cache;
            return $cache;
        }

        $this->_post = $this->hasOne('App\Models\Post', 'id', 'post_id')->first();

        return setCache($key, $this->_post, Carbon::now()->addDay());
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

    public function renderComments($sort, $context)
    {
        if (empty($sort))
            $sort = "best";

        $count = $this->childCount();
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
        $commentlist->prepareForContext($this, $sort, $expire, $context);
        return $commentlist->print_comments();
    }

    public function sortChildComments($sort, $limit, $cacheExpire)
    {
        $key = generateCacheKeyWithId("post", "comment-parents-$sort-l-$limit", $this->id);
        if ($cacheExpire != 0) {
            if (hasCache($key, $cache))
                return $cache;
        }

        $query = "GetBestComments(0, $this->id, 0, 10)";

        if ($sort == "controversial")
            $query = "GetControversialComments(0, $this->id, 0, 10)";
        else if ($sort == "hot")
            $query = "GetHotComments(0, $this->id, 0, 10)";
        else if ($sort == "new")
            $query = "GetNewComments(0, $this->id, 0, 10)";
        else if ($sort == "top")
            $query = "GetTopComments(0, $this->id, 0, 10)";

        $hydrated = Comment::hydrateRaw('CALL ' . $query);
        if ($cacheExpire == 0)
            return $hydrated;
        return setCacheWithSeconds($key, $hydrated, $cacheExpire);
    }

}

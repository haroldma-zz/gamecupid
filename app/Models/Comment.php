<?php namespace App\Models;

use Auth;
use App\Enums\VoteStates;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


/**
* Comment Model
*
* @uses     Illuminate\Database\Eloquent\Model
* @category Models
*/
class Comment extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';


    /**
     * Private variables
     *
     * @var integer|null
     */
    private $_commentCount  = -1;
    private $_upvoteCount   = -1;
    private $_downvoteCount = -1;
    private $_isUpvoted     = null;
    private $_isDownvoted   = null;
    private $_post          = null;


    /**
     * Function to get the full url to the comment
     *
     * @see     App\Models\Post::getPermalink()
     * @return  string Fully qualified URL to the comment
     */
    public function getPermalink()
    {
        return $this->post()->getPermalink() . hashId($this->id);
    }


    /**
     * Create a CommentVote instance.
     *
     * @see     App\Enums\VoteStates;
     * @param   integer $state The state the vote should be set to.
     * @return  Response
     */
    public function castVote($state)
    {
        $vote             = new CommentVote;
        $vote->comment_id = $this->id;
        $vote->user_id    = Auth::id();
        $vote->state      = $state;

        return $vote->save();
    }


    /**
     * Get the total votes of the comment.
     *
     * This function does not return the _actual_ amount of total
     * votes, but substracts the total amount of downvotes from the
     * total amount of upvotes.
     *
     * @see     $this->upvoteCount()
     * @see     $this->downvoteCount()
     * @return  integer
     */
    public function totalVotes()
    {
        return $this->upvoteCount() - $this->downvoteCount();
    }


    /**
     * Get the amount of child comments of the comment.
     *
     * Will return integer from cache if it's set, otherwise the function will
     * set it.
     *
     * @uses    $this->_commentCount
     * @uses    generateCacheKeyWithId()
     * @uses    getCache()
     * @uses    setCacheCount()
     * @uses    $this->children()
     * @return  integer
     */
    public function childCount()
    {
        if ($this->_commentCount != -1)
            return $this->_commentCount;

        $key   = generateCacheKeyWithId("comment", "childrenCount", $this->id);
        $cache = getCache($key);

        if ($cache != null)
        {
            $this->_commentCount = $cache;
            return $this->_commentCount;
        }

        $this->_commentCount = $this->children()->count();

        return setCacheCount($key, $this->_commentCount);
    }


    /**
     * Get CommentVote instances associated with the comment where the
     * state is set to UP.
     *
     * @see     App\Enums\VoteStates
     * @see     $this->votes()
     * @return  array   Returns an array of instances of App\Models\Commentvote
     */
	public function upvotes()
    {
        return $this->votes()->where('state', VoteStates::UP)->get();
    }


    /**
     * Get the total amount of upvotes.
     *
     * @uses    $this->_upvoteCount
     * @uses    generateCacheKeyWithId()
     * @uses    hasCache()
     * @uses    $this->votes()
     * @uses    App\Enums\VoteStates
     * @uses    setCacheCount()
     * @return  integer
     */
    public function upvoteCount()
    {
        if ($this->_upvoteCount != -1)
            return $this->_upvoteCount;

        $key   = generateCacheKeyWithId("comment", "upvoteCount", $this->id);

        if (hasCache($key, $cache))
        {
            $this->_upvoteCount = $cache;
            return $this->_upvoteCount;
        }

        $this->_upvoteCount = $this->votes()->where('state', VoteStates::UP)->count();

        return setCacheCount($key, $this->_upvoteCount);
    }


    /**
     * Get CommentVote instances associated with the comment where the
     * state is set to DOWN.
     *
     * @see     App\Enums\VoteStates
     * @see     $this->votes()
     * @return  array   Returns an array of instances of App\Models\Commentvote
     */
    public function downvotes()
    {
        return $this->votes()->where('state', VoteStates::DOWN)->get();
    }


    /**
     * Get the total amount of downvotes.
     *
     * @uses    $this->_downvoteCount
     * @uses    generateCacheKeyWithId()
     * @uses    hasCache()
     * @uses    $this->votes()
     * @uses    App\Enums\VoteStates
     * @uses    setCacheCount()
     * @return  integer
     */
    public function downvoteCount()
    {
        if ($this->_downvoteCount != -1)
            return $this->_downvoteCount;

        $key   = generateCacheKeyWithId("comment", "downvoteCount", $this->id);
        if (hasCache($key, $cache))
        {
            $this->_downvoteCount = $cache;
            return $this->_downvoteCount;
        }

        $this->_downvoteCount = $this->votes()->where('state', VoteStates::DOWN)->count();
        return setCacheCount($key, $this->_downvoteCount);
    }


    /**
     * Checks if the comment is upvoted by the logged in user.
     *
     * If there is no user logged in, return false.
     *
     * @uses    Auth
     * @uses    $this->_isUpvoted
     * @uses    generateAuthCacheKeyWithId()
     * @uses    hasCache()
     * @uses    App\Models\User::commentVotes()
     * @uses    App\Enums\VoteStates;
     * @uses    setCache()
     * @uses    Carbon::now()
     * @return  bool
     */
    public function isUpvoted()
    {
        if (!Auth::check())
            return false;

        if ($this->_isUpvoted != null)
            return $this->_isUpvoted;

        $key   = generateAuthCacheKeyWithId("comment", "isUpvoted", $this->id);

        if (hasCache($key, $cache))
        {
            $this->_isUpvoted = $cache;
            return $cache;
        }

        $this->_isUpvoted = Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::UP)
                            ->first() != null;

        return setCache($key, $this->_isUpvoted, Carbon::now()->addDay());
    }


    /**
     * Checks if the comment is downvoted by the logged in user.
     *
     * If there is no user logged in, return false.
     *
     * @uses    Auth
     * @uses    $this->_isDownvoted
     * @uses    generateAuthCacheKeyWithId()
     * @uses    hasCache()
     * @uses    App\Models\User::commentVotes()
     * @uses    App\Enums\VoteStates;
     * @uses    setCache()
     * @uses    Carbon::now()
     * @return  bool
     */
    public function isDownvoted()
    {
        if (!Auth::check())
            return false;

        if ($this->_isDownvoted != null)
            return $this->_isDownvoted;

        $key   = generateAuthCacheKeyWithId("comment", "isDownvoted", $this->id);

        if (hasCache($key, $cache))
        {
            $this->_isDownvoted = $cache;
            return $cache;
        }

        $this->_isDownvoted = Auth::user()->commentVotes()->where('comment_id', $this->id)->where('state', VoteStates::DOWN)
                            ->first() != null;

        return setCache($key, $this->_isDownvoted, Carbon::now()->addDay());
    }


    /**
     * Renders comments
     *
     * Harryyyy
     *
     * @param string    $sort      Description
     * @param string    $context   Description
     *
     * @return mixed
     */
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


    /**
     * Sorts child comments
     *
     * Harryyyyyy
     *
     * @param string    $sort           Description
     * @param integer   $limit          Description
     * @param integer   $cacheExpire    Description
     *
     * @return mixed Value.
     */
    public function sortChildComments($sort, $limit, $cacheExpire)
    {
        $key = generateCacheKeyWithId("post", "comment-parents-$sort-l-$limit", $this->id);

        if ($cacheExpire != 0)
        {
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


	/**
     * User relation
     *
     * Returns an instance of User associated to the comment.
     *
     * @uses    App\Models\User
     * @return  User
     */
	public function user()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}


    /**
     * Post relation
     *
     * Returns an instance of Post associated to the comment.
     *
     * @uses    $this->_post
     * @uses    generateCacheKeyWithId()
     * @uses    $this->post_id
     * @uses    hasCache()
     * @uses    App\Models\Post
     * @uses    setCache()
     * @uses    Carbon::now()
     * @return  Post
     */
	public function post()
	{
        if ($this->_post != null)
            return $this->_post;

        $key = generateCacheKeyWithId("model", "post", $this->post_id);

        if (hasCache($key, $cache))
        {
            $this->_post = $cache;
            return $cache;
        }

        $this->_post = $this->hasOne('App\Models\Post', 'id', 'post_id')->first();

        return setCache($key, $this->_post, Carbon::now()->addDay());
	}


    /**
     * Children relation
     *
     * Returns an array of Comment instances associated to the comment.
     *
     * @uses    App\Models\Comment
     * @return  array   Array of Comment instances
     */
	public function children()
	{
		return $this->hasMany('App\Models\Comment', 'parent_id', 'id');
	}


    /**
     * Parent relation
     *
     * Returns the parent of the comment (if it has one).
     *
     * @uses    App\Models\Comment
     * @return  Comment
     */
	public function parent()
	{
		return $this->hasOne('App\Models\Comment', 'id', 'parent_id');
	}


    /**
     * Votes relation
     *
     * Returns an array of CommentVote instances associated to the comment.
     *
     * @uses    App\Models\CommentVote
     * @return  array   Array of CommentVote instances
     */
    public function votes()
    {
        return $this->hasMany('App\Models\CommentVote', 'comment_id', 'id');
    }
}

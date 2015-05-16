<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'game_sessions';


    /**
    *
    * Vars
    *
    **/
    private $_commentCount  = -1;
    private $_upvoteCount   = -1;
    private $_downvoteCount = -1;


	/**
	*
	* Relations
	*
	**/
	public function participants()
	{
		return $this->hasMany('App\Models\GameSessionParticipant', 'game_session_id', 'id');
	}

	public function post()
	{
		return $this->hasOne('App\Models\Post', 'id', 'post_id');
	}

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'game_session_id', 'id');
    }


	/**
	*
	* Functions
	*
	**/
    public function commentCount()
    {
        if ($this->_commentCount != -1)
            return $this->_commentCount;

        $key   = generateCacheKeyWithId("gamesession", "commentCount", $this->id);
        if (hasCache($key, $cache)) {
            $this->_commentCount = $cache;
            return $this->_commentCount;
        }

        $this->_commentCount = $this->comments()->count();
        return setCacheCount($key, $this->_commentCount);
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
}
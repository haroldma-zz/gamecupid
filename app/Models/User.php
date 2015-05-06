<?php namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

// Entrust
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	// Entrust
	use EntrustUserTrait;


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];


	/**
	*
	* Relations
	*
	**/
	public function profiles()
	{
		return $this->hasMany('App\Models\Profile', 'user_id', 'id');
	}

	public function posts()
	{
		return $this->hasMany('App\Models\Post', 'user_id', 'id');
	}

	public function accepts()
	{
		return $this->hasMany('App\Models\Accept', 'user_id', 'id');
	}

	public function reps()
	{
		return $this->hasMany('App\Models\Rep', 'user_id', 'id');
	}

	public function rNotifications()
	{
		return $this->hasMany('App\Models\Notification', 'to_id', 'id');
	}

	public function sNotifications()
	{
		return $this->hasMany('App\Models\Notification', 'from_id', 'id');
	}

	public function inviteVotes()
	{
		return $this->hasMany('App\Models\InviteVote', 'user_id', 'id');
	}

	public function commentVotes()
	{
		return $this->hasMany('App\Models\CommentVote', 'user_id', 'id');
	}

	// Function to return total rep amount (lazy loaded)
    private $_rep = null;
	public function rep($useCache = true)
	{
        if ($this->_rep != null)
            return $this->_rep;

        $key = generateCacheKeyWithId("user", "rep", $this->id);
        if ($useCache && hasCache($key, $cache)) {
            $this->_rep = $cache;
            return $cache;
        }

		if ($this->_rep == null)
            $this->_rep = 0;

		$this->_rep = DB::SELECT(DB::RAW("SELECT SUM(x.total) as total FROM
                    (SELECT
                      (SELECT sum(amount) FROM rep_events WHERE id=rep_event_id) as total
                      FROM reps WHERE user_id=?) x"), [$this->id])[0]->total;

		if ($useCache)
        	return setCache($key, $this->_rep, Carbon::now()->addDay());
		return $this->_rep;
	}

	// Function to return top 10 players
    private static $_topPlayers = [];
	public static function topPlayers($useCache = true)
	{
        if (Self::$_topPlayers != null)
            return Self::$_topPlayers;

        $key = generateCacheKey("user", "topgamers");
        if ($useCache && hasCache($key, $cache)) {
            Self::$_topPlayers = $cache;
            return $cache;
        }

		if (count(Self::$_topPlayers) == 0)
            Self::$_topPlayers = [];

		Self::$_topPlayers =  User::hydrateRaw("call GetBestGamers(10)");

		if ($useCache)
        	return setCache($key, Self::$_topPlayers, Carbon::now()->addDay());
		return Self::$_topPlayers;
	}

    private $factor = 3.0;

    // Function to calculate level
    public function level()
    {
        $rep = $this->rep();
        return max(floor(pow($rep, 1/$this->factor)), 1);
    }

    public function repToNextLevel()
    {
        $level = $this->level() + 1;
        return $level^$this->factor;
    }
}

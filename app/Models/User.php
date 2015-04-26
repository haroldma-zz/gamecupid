<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

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

	public function invites()
	{
		return $this->hasMany('App\Models\Invite', 'user_id', 'id');
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

    private $repAmount = -1;

	// Function to return total rep amount (lazy loaded)
	public function rep()
	{
        // if the field is already set then return it
		if ($this->repAmount > -1)
            return $this->repAmount;

        // otherwise calculate total rep
        $this->repAmount = 0;

		foreach ($this->reps as $rep)
		{
            $this->repAmount += $rep->event->amount;
		}

		return $this->repAmount;
	}

    private $factor = 3.0;

    // Function to calculate level
    public function level()
    {
        $rep = $this->rep();
        return 1+floor(pow($rep, 1/$this->factor));
    }

    public function repToNextLevel()
    {
        $level = level();
        return $level^$this->factor;
    }
}

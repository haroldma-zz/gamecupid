<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invites';

	/**
	*
	* Relations
	*
	**/
	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id');
	}

	public function game()
	{
		return $this->belongsTo('App\Models\Game', 'game_id');
	}

	public function console()
	{
		return $this->belongsTo('App\Models\Console', 'console_id');
	}

	public function platform()
	{
		return $this->belongsTo('App\Models\Platform', 'platform_id');
	}

}
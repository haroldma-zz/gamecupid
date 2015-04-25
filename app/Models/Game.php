<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'games';


	/**
	*
	* Relations
	*
	**/
	public function developers()
	{
		return $this->hasMany('App\Models\Developer', 'id', 'developer_id');
	}

	public function publishers()
	{
		return $this->hasMany('App\Models\Publisher', 'id', 'publisher_id');
	}

	public function invites()
	{
		return $this->hasMany('App\Models\Invite', 'game_id', 'id');
	}

	public function consoles()
	{
		return $this->hasMany('App\Models\AvailableConsole', 'game_id', 'id');
	}
}
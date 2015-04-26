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
		return $this->hasMany('App\Models\GameDeveloper', 'game_id', 'id');
	}

	public function publishers()
	{
		return $this->hasMany('App\Models\GamePublisher', 'game_id', 'id');
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
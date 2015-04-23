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
	public function developer()
	{
		return $this->belongsTo('App\Models\Developer', 'developer_id', 'id');
	}

	public function publisher()
	{
		return $this->belongsTo('App\Models\Publisher', 'publisher_id', 'id');
	}

	public function invites()
	{
		return $this->hasMany('App\Models\Invite', 'game_id', 'id');
	}
}
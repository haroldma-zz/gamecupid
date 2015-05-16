<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSessionParticipant extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'game_session_participants';


	/**
	*
	* Relations
	*
	**/
	public function user()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}
}
<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamePublisher extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'game_publishers';


	/**
	*
	* Relations
	*
	**/
	public function publisher()
	{
		return $this->hasOne('App\Models\Publisher', 'id', 'publisher_id');
	}

}

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameDeveloper extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'game_developers';


	/**
	*
	* Relations
	*
	**/
	public function developer()
	{
		return $this->hasOne('App\Models\Developer', 'id', 'developer_id');
	}

}

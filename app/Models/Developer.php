<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Developer extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'developers';


	/**
	*
	* Relations
	*
	**/
	public function games()
	{
		return $this->hasMany('App\Model\Game', 'developer_id', 'id');
	}
}
<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'platforms';


	/**
	*
	* Relations
	*
	**/
	public function consoles()
	{
		return $this->hasMany('App\Model\Console', 'platform_id', 'id');
	}
}
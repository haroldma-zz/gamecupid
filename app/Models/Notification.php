<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'notifications';


	/**
	*
	* Relations
	*
	**/
	public function from()
	{
		return $this->hasOne('App\Models\User', 'id', 'from_id');
	}

	public function to()
	{
		return $this->hasOne('App\Models\User', 'id', 'to_id');
	}

}
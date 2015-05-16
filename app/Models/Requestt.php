<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requestt extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'requests';


	/**
	*
	* Relations
	*
	**/
	public function requester()
	{
		return $this->hasOne('App\Models\User', 'id', 'requester_id');
	}

	public function post()
	{
		return $this->hasOne('App\Models\Post', 'id', 'post_id');
	}
}

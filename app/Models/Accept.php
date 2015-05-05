<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accept extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'accepts';


	/**
	*
	* Relations
	*
	**/
	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

	public function post()
	{
		return $this->belongsTo('App\Models\Post', 'post_id', 'id');
	}
}
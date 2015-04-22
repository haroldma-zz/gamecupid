<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'profiles';


	/**
	*
	* Relations
	*
	**/
	public function user()
	{
		return $this->belongsTo('App\Model\User', 'user_id', 'id');
	}

	public function platform()
	{
		return $this->belongsTo('App\Model\Platform', 'platform_id', 'id');
	}
}
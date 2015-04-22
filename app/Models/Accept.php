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

	public function invite()
	{
		return $this->belongsTo('App\Models\Invite', 'invite_id', 'id');
	}
}
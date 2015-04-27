<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';


	/**
	*
	* Relations
	*
	**/
	public function user()
	{
		return $this->hasOne('App\Models\User', 'id', 'user_id');
	}

	public function invite()
	{
		return $this->hasOne('App\Models\Invite', 'id', 'invite_id');
	}

	public function children()
	{
		return $this->hasMany('App\Models\Comment', 'parent_id', 'id');
	}

	public function parent()
	{
		return $this->hasOne('App\Models\Comment', 'id', 'parent_id');
	}

}

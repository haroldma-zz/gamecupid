<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rep extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reps';


	/**
	*
	* Relations
	*
	**/
	public function event()
	{
		return $this->hasOne('App\Models\RepEvent', 'id', 'rep_event_id');
	}

}

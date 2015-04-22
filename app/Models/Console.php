<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Console extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'consoles';


	/**
	*
	* Relations
	*
	**/
	public function platform()
	{
		return $this->belongsTo('App\Models\Platform', 'platform_id', 'id');
	}
}
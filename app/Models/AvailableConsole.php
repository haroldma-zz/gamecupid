<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* AvailableConsole model
*
* This model is used to check for which consoles
* a game is available.
*
* @uses     Illuminate\Database\Eloquent\Model
* @category Models
*/
class AvailableConsole extends Model
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'available_console';


    /**
     * Get the console instance associated with the game.
     *
     * @uses  	App\Models\Console
     * @return 	Console
     */
	public function console()
	{
		return $this->hasOne('App\Models\Console', 'id', 'console_id');
	}
}

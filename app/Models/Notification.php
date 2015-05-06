<?php namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Enums\NotificationTypes;
use Kumuwai\DataTransferObject\Laravel5DTO;

class Notification extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'notifications';

    public function title()
    {
        $type = $this->type;
        if ($type == NotificationTypes::REP)
            return "rep";
        if ($type == NotificationTypes::COMMENT_REPLY)
            return "comment reply";
        if ($type == NotificationTypes::POST_COMMENT)
            return "comment on your post";
    }

    public function createDto()
    {
        $type = $this->type;
        if ($type == NotificationTypes::REP)
            $description = sprintf("%+d",$this->repEvent()->amount)." rep: ". $this->repEvent()->event;
        else if ($type == NotificationTypes::COMMENT_REPLY)
            $description = $this->comment()->post()->title;
        else if ($type == NotificationTypes::POST_COMMENT)
            $description = $this->post()->title;

        return new Laravel5DTO([
            'title' => $this->title(),
            'description' => $description,
            'read' => $this->read
        ]);
    }

	/**
	*
	* Relations
	*
	**/
    private $_thing = null;

	public function repEvent()
	{
        if ($this->_thing != null)
            return $this->_thing;

        $key = generateAuthCacheKeyWithId("model", "repEvent", $this->thing_id);
        if (hasCache($key, $cache)) {
            $this->_thing = $cache;
            return $cache;
        }

        $this->_thing = $this->hasOne('App\Models\RepEvent', 'id', 'thing_id')->first();
		return setCache($key, $this->_thing, Carbon::now()->addDay());
	}

    public function comment()
    {
        if ($this->_thing != null)
            return $this->_thing;

        $key = generateAuthCacheKeyWithId("model", "comment", $this->thing_id);
        if (hasCache($key, $cache)) {
            $this->_thing = $cache;
            return $cache;
        }

        $this->_thing = $this->hasOne('App\Models\Comment', 'id', 'thing_id')->first();
        return setCache($key, $this->_thing, Carbon::now()->addDay());
    }

    public function post()
    {
        if ($this->_thing != null)
            return $this->_thing;

        $key = generateAuthCacheKeyWithId("model", "post", $this->thing_id);
        if (hasCache($key, $cache)) {
            $this->_thing = $cache;
            return $cache;
        }

        $this->_thing = $this->hasOne('App\Models\Post', 'id', 'thing_id')->first();
        return setCache($key, $this->_thing, Carbon::now()->addDay());
    }

	public function from()
	{
		return $this->hasOne('App\Models\User', 'id', 'from_id');
	}

	public function to()
	{
		return $this->hasOne('App\Models\User', 'id', 'to_id');
	}

}

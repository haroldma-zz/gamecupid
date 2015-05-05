<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostVote extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'post_votes';

    /**
     *
     * Relations
     *
     **/
    public function post()
    {
        return $this->belongsTo('App\Models\Post', 'id', 'post_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }
}

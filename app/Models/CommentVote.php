<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentVote extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comment_votes';

    /**
     *
     * Relations
     *
     **/
    public function comment()
    {
        return $this->belongsTo('App\Models\Comment', 'id', 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }
}

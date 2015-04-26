<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InviteVote extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invite_votes';

    /**
     *
     * Relations
     *
     **/
    public function invite()
    {
        return $this->belongsTo('App\Models\Invite', 'id', 'invite_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }
}

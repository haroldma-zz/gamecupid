<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crew extends Model {

    /**
     *
     * Relations
     *
     **/
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function members()
    {
        return $this->hasMany('App\Models\CrewMember', 'crew_id', 'id');
    }

    public function requests()
    {
        return $this->hasMany('App\Models\CrewRequests', 'crew_id', 'id');
    }
}

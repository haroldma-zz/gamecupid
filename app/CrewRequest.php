<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class CrewRequest extends Model {

    /**
     *
     * Relations
     *
     **/
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function crew()
    {
        return $this->belongsTo('App\Models\Crew', 'crew_id', 'id');
    }

}

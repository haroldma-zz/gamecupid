<?php

use Illuminate\Support\Facades\Auth;

function giveRepAndNotified($repEvent, $userId = null) {
    $rep               = new Rep;
    $rep->rep_event_id = $repEvent;
    $rep->user_id      = $userId || Auth::user()->id;
    if ($rep->save()) {
        notifiedAboutRepEvent($repEvent);

        // invalidate the rep count
        $key = generateCacheKeyWithId("user", "rep", $rep->user_id);
        invalidateCache($key);

        return true;
    }
    else
        return false;
}
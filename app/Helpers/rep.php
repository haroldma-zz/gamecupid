<?php

use Illuminate\Support\Facades\Auth;

function giveRepAndNotified($repEvent, $userId = null) {
    $rep               = new Rep;
    $rep->rep_event_id = $repEvent;
    $rep->user_id      = $userId != null ? $userId : Auth::user()->id;
    if ($rep->save()) {
        notifiedAboutRepEvent($repEvent);
        return true;
    }
    else
        return false;
}
<?php

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Enums\NotificationTypes;

function createNotification($thingId, $userId = null) {
    $not              = new Notification;
    $not->to_id       = $userId || Auth::user()->id;
    $not->thing_id    = $thingId;
    $not->notified    = false;
    $not->read    = false;
    return $not;
}

function notifiedAboutComment($commentId, $userId = null) {
    $not              = createNotification($commentId, $userId);
    $not->type        = NotificationTypes::COMMENT_REPLY;
    return $not->save();
}

function notifiedAboutRepEvent($repEvent, $userId = null) {
    $not              = createNotification($repEvent, $userId);
    $not->type        = NotificationTypes::REP;
    return $not->save();
}
<?php

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Enums\NotificationTypes;

function createNotification($thingId, $userId = null)
{
    $not           = new Notification;
    $not->from_id  = Auth::id();
    $not->to_id    = ($userId != null ? $userId : Auth::id());
    $not->thing_id = $thingId;
    $not->notified = false;
    $not->read     = false;
    return $not;
}

function notifiedAboutCommentReply($commentId, $userId = null)
{
    $not           = createNotification($commentId, $userId);
    $not->type     = NotificationTypes::COMMENT_REPLY;
    return $not->save();
}

function notifiedAboutComment($commentId, $userId = null)
{
    $not           = createNotification($commentId, $userId);
    $not->type     = NotificationTypes::POST_COMMENT;
    return $not->save();
}

function notifiedAboutRepEvent($repEvent, $userId = null)
{
    $not           = createNotification($repEvent, $userId);
    $not->type     = NotificationTypes::REP;
    return $not->save();
}

function notifiedAboutInviteRequest($postId, $userId = null)
{
    $not           = createNotification($postId, $userId);
    $not->type     = NotificationTypes::INVITE_REQUEST;
    return $not->save();
}

function notifiedAboutDeclinedInviteRequest($postId, $userId = null)
{
    $not           = createNotification($postId, $userId);
    $not->type     = NotificationTypes::DECLINED_INVITE;
    return $not->save();
}
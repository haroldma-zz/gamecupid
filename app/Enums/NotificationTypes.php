<?php namespace App\Enums;

abstract class NotificationTypes
{
	const REP             = 0;
	const COMMENT_REPLY   = 1;
	const POST_COMMENT    = 2;
	const PM              = 3;
	const INVITE_REQUEST  = 4;
	const DECLINED_INVITE = 5;
	const ACCEPTED_INVITE = 6;
}

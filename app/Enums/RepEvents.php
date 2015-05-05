<?php namespace App\Enums;
/**
 * User: harry
 * Date: 4/26/15
 * Time: 11:54 AM
 */

abstract class RepEvents
{
    const NONE                        = 0;
    const REGISTERED                  = 1;
    const CONFIRMED_EMAIL             = 2;
    const ADD_PROFILE_PICTURE         = 3;
    const ADD_BIO                     = 4;
    const COMPLETE_PROFILE            = 5;
    const CREATED_POST                = 6;
    const APPROVED_FOR_INVITE         = 7;
    const DID_NOT_PARTICIPATE_ON_GAME = 8;
    const CREATED_CREW                = 9;
    const JOINED_CREW                 = 10;
    const LEFT_CREW                   = 11;
    const VERIFIED_PROFILE            = 12;
}
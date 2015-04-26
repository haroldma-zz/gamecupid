<?php namespace App\Enums;
/**
 * User: harry
 * Date: 4/26/15
 * Time: 11:54 AM
 */

abstract class AjaxVoteResults
{
    const NONE = 0;
    const NORMAL   = 1;
    const UNVOTED  = 2;
    const VOTE_SWITCH  = 3;
    const UNAUTHORIZED  = 4;
    const ERROR  = 5;
}
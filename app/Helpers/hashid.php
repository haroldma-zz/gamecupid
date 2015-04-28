<?php

use Vinkla\Hashids\Facades\Hashids;

function decodeHashId($id)
{
    return Hashids::decode($id)[0];
}
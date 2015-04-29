<?php

use Vinkla\Hashids\Facades\Hashids;

function decodeHashId($id)
{
    $ids = Hashids::decode($id);

    if (count($ids) == 0)
        return 0;

    return $ids[0];
}

function hashId($id)
{
    return Hashids::encode($id);
}
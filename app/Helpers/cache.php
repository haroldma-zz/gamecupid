<?php

/**
*
* Helper functions related to caching
*
**/

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

function generateCacheKey($model, $action)
{
    return 'm_'. $model . '_ac_' . $action;
}

function generateAuthCacheKey($model, $action, $auth)
{
    return generateCacheKey($model, $action) . '_au_' . $auth;
}

function generateCacheKeyWithId($model, $action, $id)
{
    return generateCacheKey($model, $action) . '_id_'. $id;
}

function generateAuthCacheKeyWithId($model, $action, $auth, $id)
{
    return generateAuthCacheKey($model, $action, $auth) . '_id_' . $id;
}

function getCache($key)
{
    if (Cache::has($key))
        return Cache::get($key);
    return null;
}

function setCache($key, $value, $expire)
{
    Cache::put($key, $value, $expire);
    return $value;
}

function setCacheCount($key, $value)
{
    Cache::put($key, $value, calculateExpiry($value));
    return $value;
}

function calculateExpiry($count)
{
    if ($count == 0)
        return Carbon::now()->addSeconds(5);
    if ($count > 50)
        return Carbon::now()->addSeconds(30);
    if ($count < 500)
        return Carbon::now()->addMinute(1);
    return Carbon::now()->addMinute(5);
}
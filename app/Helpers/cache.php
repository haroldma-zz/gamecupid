<?php

/**
*
* Helper functions related to caching
*
**/

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

function generateCacheKey($model, $action)
{
    return 'm_'. $model . '_ac_' . $action;
}

function generateAuthCacheKey($model, $action)
{
    return generateCacheKey($model, $action) . '_au_' . Auth::user()->id;
}

function generateCacheKeyWithId($model, $action, $id)
{
    return generateCacheKey($model, $action) . '_id_'. $id;
}

function generateAuthCacheKeyWithId($model, $action, $id)
{
    return generateAuthCacheKey($model, $action) . '_id_' . $id;
}

function invalidateCache($key)
{
    Cache::forget($key);
}

function getCache($key)
{
    return Cache::get($key);
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
    if ($count < 50)
        return Carbon::now()->addSeconds(5);
    if ($count < 100)
        return Carbon::now()->addSeconds(30);
    if ($count < 500)
        return Carbon::now()->addMinute(1);
    return Carbon::now()->addMinute(5);
}
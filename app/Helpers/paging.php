<?php

use Carbon\Carbon;

function stringToFromDate($t) {
    if ($t == 'hour')
        return Carbon::now()->subHour();
    if ($t == 'week')
        return Carbon::now()->subWeek();
    if ($t == 'month')
        return Carbon::now()->subMonth();
    if ($t == 'year')
        return Carbon::now()->subYear();
    if ($t == 'all') // remind me to update this in a 1000 years
        return Carbon::now()->subYears(1000);
    return Carbon::now()->subDay();
}
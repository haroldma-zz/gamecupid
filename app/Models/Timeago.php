<?php namespace App\Models;

class Timeago
{
  public static function convert($datetime)
  {
      $time    = strtotime($datetime);
      $timeago = time() - $time;

      if($timeago < 1)
      {
          return '1 second ago';
      }

      $condition = array(
                  12 * 30 * 24 * 60 * 60  =>  'year',
                  30 * 24 * 60 * 60       =>  'month',
                  24 * 60 * 60            =>  'day',
                  60 * 60                 =>  'hour',
                  60                      =>  'minute',
                  1                       =>  'second'
      );

      foreach($condition as $secs => $str)
      {
          $d = $timeago / $secs;

          if($d >= 1)
          {
            $r = round($d);
            return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
          }
      }
  }
}
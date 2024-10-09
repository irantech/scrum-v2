<?php

use App\Models\holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

if (!function_exists('helperGetSmsTemplate')) {
    function helperGetSmsTemplate($key = '')
    {
        if (!$key) {
            return false;
        }
        $sms = DB::table('sms_templates')->select('template', 'params' , 'title')->where('key', $key)->first();
        return $sms;
    }
}

if (!function_exists('calculatePeriodOfTimeInMinutes')) {
    function calculatePeriodOfTimeInMinutes($day = 0 , $time = 0)
    {
        if($day < 0 ) {
            $day  = 0;
        }
        [$hours, $minutes , $seconds] = explode(':', $time);

        if($hours > 8 ) {

            $hours  = ( 8 * $hours ) / 24 ;
        }


        $timeInMinute =  (int)$hours * 60 + (int)$minutes;

        $dayInMinute = $day  * env("EACH_DAY_MINUTE_TIME") ;

        return $timeInMinute + $dayInMinute ;

    }
}

if (!function_exists('weekDays')) {
    function weekDays($start = 0 , $end = 0)
    {
        $holidays = holiday::select('date')->whereBetween('created_at', [$start, $end])->get()->toArray();
      
        $diff_day = $start->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
            return $date->isWeekday() && !in_array($date, $holidays);
        }, $end);
        return --$diff_day ;

    }
}

if (!function_exists('howManyDaysAndHowMuchTimeInMinute')) {
    function howManyDaysAndHowMuchTimeInMinute($minutes)
    {
       
        $how_many_days = intdiv($minutes , enV("EACH_DAY_MINUTE_TIME")) ;

        $remaining_minutes = $minutes - ( $how_many_days * enV("EACH_DAY_MINUTE_TIME") );
        if ($remaining_minutes !=  0 )
            $how_much_time = intdiv($remaining_minutes, 60).':'. ($remaining_minutes % 60) . ':00' ;
        return $how_many_days . ' ' . $how_much_time;

    }
}


<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

class IndoTime extends Time
{
    private static function explodeTimestamp(string $timestamp): array
    {
        $arr_timestamp = explode(' ',$timestamp);
        $arr_date = explode('-',$arr_timestamp[0]);
        $arr_time = explode(':',end($arr_timestamp));

        return [
            'year' => (int) $arr_date[0],
            'month' => (int) $arr_date[1],
            'day' => (int) $arr_date[2],
            'hour' => (int) $arr_time[0],
            'minutes' => (int) $arr_time[1],
            'seconds' => (int) $arr_time[2]
        ];
    }

    public static function toIndoLocalizedString(? string $timestamp, string $format = 'dd MMMM YYYY, HH:mm:ss'): ? string
    {
        if($timestamp === null) return null;

        [
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'hour' => $hour,
            'minutes' => $minutes,
            'seconds' => $seconds
        ] = static::explodeTimestamp($timestamp);

        return static::create($year, $month, $day, $hour, $minutes, $seconds, 'Asia/Jakarta', 'id_ID')->toLocalizedString($format);
    }
}

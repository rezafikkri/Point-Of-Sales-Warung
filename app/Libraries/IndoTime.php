<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

class IndoTime extends Time
{
    private $format = 'dd MMM YYYY, HH:mm:ss';

    private $year;
    private $month;
    private $day;
    private $hour;
    private $minutes;
    private $seconds;

    private function explodeTimestamp(string $timestamp): void
    {
        $timestamps = explode(' ',$timestamp);
        $dates = explode('-',$timestamps[0]);
        $times = explode(':',end($timestamps));

        $this->year = (int) $dates[0];
        $this->month = (int) $dates[1];
        $this->day = (int) $dates[2];
        $this->hour = (int) $times[0];
        $this->minutes = (int) $times[1];
        $this->seconds = (int) $times[2];
    }

    public function toIndoLocalizedString(?string $timestamp): ?string
    {
        if($timestamp === null) return null;

        $this->explodeTimestamp($timestamp);

        return $this->create(
            $this->year,
            $this->month,
            $this->day,
            $this->hour,
            $this->minutes,
            $this->seconds,
            'Asia/Jakarta',
            'id_ID'
        )->toLocalizedString($this->format);
    }

    public function setFormat(string $value): void
    {
        $this->format = $value;
    }
}

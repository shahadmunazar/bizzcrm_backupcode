<?php

namespace App\Services;

use Carbon\Carbon;

class BusinessDayService
{
    /**
     * Get the business date for a given timestamp.
     * Uses standard midnight-to-midnight Asia/Kolkata timezone.
     *
     * @param mixed $timestamp
     * @return \Carbon\Carbon
     */
    public static function getBusinessDateForTimestamp($timestamp = null)
    {
        $date = $timestamp ? Carbon::parse($timestamp) : Carbon::now();
        return $date->startOfDay();
    }

    /**
     * Get the current business date.
     *
     * @return \Carbon\Carbon
     */
    public static function getCurrentBusinessDate()
    {
        return self::getBusinessDateForTimestamp(Carbon::now());
    }

    /**
     * Get the standard formatted string (Y-m-d) for the current business date.
     *
     * @return string
     */
    public static function getCurrentBusinessDateString()
    {
        return self::getCurrentBusinessDate()->format('Y-m-d');
    }

    /**
     * Get the exact start and end timestamps (Carbon instances) for a given business date.
     * Uses standard midnight-to-midnight.
     *
     * @param string|\Carbon\Carbon $businessDate
     * @return array ['start' => Carbon, 'end' => Carbon]
     */
    public static function getBusinessDayRange($businessDate)
    {
        $date = is_string($businessDate) ? Carbon::parse($businessDate) : $businessDate->copy();
        $date->startOfDay();

        $start = $date->copy();
        $end = $date->copy()->endOfDay();

        return [
            'start' => $start,
            'end' => $end
        ];
    }
}

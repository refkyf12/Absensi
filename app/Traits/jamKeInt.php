<?php

namespace App\Traits;

trait jamKeInt
{
    public function timeToInteger(string $timeString)
    {
        // Use strtotime() to convert the time string to a Unix timestamp
        $timestamp = strtotime($timeString);

        // Use date() to extract the hours and minutes from the timestamp
        $hours = date('G', $timestamp);
        $minutes = date('i', $timestamp);

        // Calculate the total number of seconds
        $totalSeconds = ($hours * 3600) + ($minutes * 60);

        // Output the result
        return $totalSeconds; // Output: 3600
    }
}

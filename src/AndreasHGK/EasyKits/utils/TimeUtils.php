<?php
/**
 *    _____                         _  __  _   _         
 *   | ____|   __ _   ___   _   _  | |/ / (_) | |_   ___ 
 *   |  _|    / _` | / __| | | | | | ' /  | | | __| / __|
 *   | |___  | (_| | \__ \ | |_| | | . \  | | | |_  \__ \
 *   |_____|  \__,_| |___/  \__, | |_|\_\ |_|  \__| |___/
 *                           |___/                        
 *          by AndreasHGK and fernanACM 
 */
declare(strict_types=1);

namespace AndreasHGK\EasyKits\utils;

use http\Exception\UnexpectedValueException;

abstract class TimeUtils{

    /**
     * @param integer $seconds
     * @return string
     */
    public static function intToTimeString(int $seconds): string{
        if($seconds < 0) throw new UnexpectedValueException("time can't be a negative value");
        if($seconds === 0) {
            return "0 seconds";
        }
        $timeString = "";
        $timeArray = [];
        if($seconds >= 86400) {
            $unit = floor($seconds / 86400);
            $seconds -= $unit * 86400;
            $timeArray[] = $unit . " days";
        }
        if($seconds >= 3600) {
            $unit = floor($seconds / 3600);
            $seconds -= $unit * 3600;
            $timeArray[] = $unit . " hours";
        }
        if($seconds >= 60) {
            $unit = floor($seconds / 60);
            $seconds -= $unit * 60;
            $timeArray[] = $unit . " minutes";
        }
        if($seconds >= 1) {
            $timeArray[] = $seconds . " seconds";
        }
        foreach($timeArray as $key => $value) {
            if($key === 0) {
                $timeString .= $value;
            } elseif($key === count($timeArray) - 1) {
                $timeString .= " and " . $value;
            } else {
                $timeString .= ", " . $value;
            }
        }
        return $timeString;
    }
}
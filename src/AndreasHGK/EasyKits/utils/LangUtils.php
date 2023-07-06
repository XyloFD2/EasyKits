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

use AndreasHGK\EasyKits\manager\DataManager;
use pocketmine\utils\TextFormat;

abstract class LangUtils{

    /**
     * Get a message from the lang.yml file
     *
     * @param string $key
     * @param bool $colorize
     * @param array $replace
     * @return string[]|string
     */
    public static function getMessage(string $key, bool $colorize = true, array $replace = []){
        $msg = DataManager::getKey(DataManager::LANG, $key, null);
        if(is_null($msg)) return $key;
        if(is_array($msg)) {
            $return = [];
            foreach($msg as $key => $msgE){
                if($msgE === false) return "";
                $msgE = self::replaceVariables($msgE, $replace);
                if($colorize) TextFormat::colorize($msgE);
                $return[$key] = $msgE;
            }
        }else{
            if($msg === false) return "";
            $msg = self::replaceVariables($msg, $replace);
            if($colorize) TextFormat::colorize($msg);
            $return = $msg;
        }
        return $return;
    }

    /**
     * @param string $text
     * @param array $variables
     * @return string
     */
    public static function replaceVariables(string $text, array $variables): string{
        foreach($variables as $variable => $replace) {
            $text = str_replace($variable, (string)$replace, $text);
        }
        return $text;
    }
}
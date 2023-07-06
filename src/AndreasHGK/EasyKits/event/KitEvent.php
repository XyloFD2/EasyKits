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

namespace AndreasHGK\EasyKits\event;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;

use AndreasHGK\EasyKits\Kit;

abstract class KitEvent extends Event implements Cancellable{

    protected $kit;

    public function __construct(Kit $kit){
        $this->kit = $kit;
    }

    /**
     * @return Kit
     */
    public function getKit(): Kit{
        return $this->kit;
    }

    /**
     * @param Kit $kit
     */
    public function setKit(Kit $kit): void{
        $this->kit = $kit;
    }

    /**
     * @return boolean
     */
    public function isCancelled(): bool{
        return false;
    }

}

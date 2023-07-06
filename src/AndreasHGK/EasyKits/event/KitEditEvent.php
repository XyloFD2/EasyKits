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

use AndreasHGK\EasyKits\Kit;

class KitEditEvent extends KitEvent{

    /**
     * @var Kit
     */
    protected $originalKit;

    public function __construct(Kit $old, Kit $new){
        parent::__construct($new);
        $this->originalKit = $old;
    }

    /**
     * @return Kit
     */
    public function getOriginalKit(): Kit{
        return $this->originalKit;
    }

}
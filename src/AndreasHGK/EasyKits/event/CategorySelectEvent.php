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

use pocketmine\player\Player;

use AndreasHGK\EasyKits\Category;

class CategorySelectEvent extends CategoryEvent{
    use PlayerEventTrait;

    public function __construct(Player $player, Category $category) {
        parent::__construct($category);
        $this->player = $player;
    }
}
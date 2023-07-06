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

use AndreasHGK\EasyKits\Category;

abstract class CategoryEvent extends Event implements Cancellable{

    protected $category;

    public function __construct(Category $category){
        $this->category = $category;
    }

    /**
     * @return Category
     */
    public function getCategory() : Category{
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void{
        $this->category = $category;
    }

    /**
     * @return boolean
     */
    public function isCancelled(): bool{
        return false;
    }

}

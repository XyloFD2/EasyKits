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

use AndreasHGK\EasyKits\Category;

class CategoryEditEvent extends CategoryEvent{

    /**
     * @var Category
     */
    protected $originalCategory;

    public function __construct(Category $old, Category $new){
        parent::__construct($new);
        $this->originalCategory = $old;
    }

    /**
     * @return Category
     */
    public function getOriginalCategory(): Category{
        return $this->originalCategory;
    }
}
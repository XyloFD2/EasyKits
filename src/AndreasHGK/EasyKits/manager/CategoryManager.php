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

namespace AndreasHGK\EasyKits\manager;

use Throwable;

use pocketmine\permission\Permissible;

use pocketmine\utils\Config;

use AndreasHGK\EasyKits\Category;
use AndreasHGK\EasyKits\EasyKits;
use AndreasHGK\EasyKits\event\CategoryCreateEvent;
use AndreasHGK\EasyKits\event\CategoryDeleteEvent;
use AndreasHGK\EasyKits\event\CategoryEditEvent;
use AndreasHGK\EasyKits\Kit;

class CategoryManager{

    public const CATEGORY_FORMAT = [
        "kits" => [],
        "locked" => true,
    ];

    /** @var array $categories */
    public static array $categories = [];


    public static function exists(string $file): bool{
        return isset(self::$categories[$file]);
    }

    /**
     * @param Permissible $permissible
     * @return Kit[]
     */
    public static function getPermittedCategoriesFor(Permissible $permissible): array{
        $categories = [];
        foreach(self::getAll() as $category){
            if($category->hasPermission($permissible)){
                $categories[] = $category;
            }
        }
        return $categories;
    }

    /**
     * @return Category[]
     */
    public static function getAll(): array{
        return self::$categories;
    }

    /**
     * @param string $name
     * @return Category|null
     */
    public static function get(string $name): ?Category{
        return isset(self::$categories[$name]) ? clone self::$categories[$name] : null;
    }

    /**
     * @param Category $old
     * @param Category $new
     * @param boolean $silent
     * @return boolean
     */
    public static function update(Category $old, Category $new, bool $silent = false): bool{
        $event = new CategoryEditEvent($old, $new);
        if(!$silent) $event->call();

        if($event->isCancelled()) return false;

        if($event->getOriginalCategory()->getName() !== $event->getCategory()->getName()) {
            self::remove($old, true);
        }
        self::$categories[$event->getCategory()->getName()] = $event->getCategory();
        return true;
    }

    /**
     * @param Category $category
     * @param boolean $silent
     * @return boolean
     */
    public static function add(Category $category, bool $silent = false): bool{
        $event = new CategoryCreateEvent($category);
        if(!$silent) $event->call();

        if($event->isCancelled()) return false;

        self::$categories[$event->getCategory()->getName()] = $event->getCategory();
        return true;
    }

    /**
     * @param Category $kit
     * @param boolean $silent
     * @return boolean
     */
    public static function remove(Category $kit, bool $silent = false): bool{
        $event = new CategoryDeleteEvent($kit);
        if(!$silent) $event->call();

        if($event->isCancelled()) return false;

        $kits = self::getCategoryFile();
        $kits->remove($event->getCategory()->getName());
        DataManager::save(DataManager::CATEGORIES);
        self::unload($event->getCategory()->getName());
        return true;
    }

    /**
     * @return void
     */
    public static function loadAll(): void{
        $file = self::getCategoryFile()->getAll();
        foreach($file as $name => $category) {
            self::load((string)$name);
        }
    }

    /**
     * @return void
     */
    public static function saveAll(): void{
        foreach(self::getAll() as $name => $category) {
            self::save((string)$name);
        }
        DataManager::save(DataManager::CATEGORIES);
    }

    /**
     * @return void
     */
    public static function reloadAll(): void{
        DataManager::reload(DataManager::CATEGORIES);
        self::unloadAll();
        self::loadAll();
    }

    /**
     * @return void
     */
    public static function unloadAll(): void{
        self::$categories = [];
    }

    public static function unload(string $kit) : void {
        unset(self::$categories[$kit]);
    }

    /**
     * @param string $name
     * @return void
     */
    public static function load(string $name): void{
        $file = self::getCategoryFile()->getAll();
        $categorydata = $file[$name];
        try{
            $category = new Category($name);
            $kits = [];
            foreach($categorydata["kits"] as $kitname){
                if(!KitManager::exists($kitname)) continue;
                $kits[$kitname] = KitManager::get($kitname);
            }
            $category->setLocked($categorydata["locked"]);
            $category->setKits($kits);
            self::$categories[$name] = $category;
        } catch(Throwable $e) {
            EasyKits::get()->getLogger()->error("failed to load category '" . $name . "'");
            EasyKits::get()->getLogger()->debug($e->getMessage());
        }
    }

    /**
     * @param string $name
     * @return void
     */
    public static function save(string $name): void{
        $file = self::getCategoryFile();
        $category = self::get($name);
        $categoryData = self::CATEGORY_FORMAT;
        foreach($category->getKits() as $kit) {
            $categoryData["kits"][] = $kit->getName();
        }
        $categoryData["locked"] = $category->isLocked();
        $file->set($category->getName(), $categoryData);
    }

    /**
     * @return Config
     */
    private static function getCategoryFile(): Config{
        return DataManager::get(DataManager::CATEGORIES);
    }

    private function __construct(){
    }
}
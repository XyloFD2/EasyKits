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

use ReflectionProperty;
use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;

use DaPigGuy\libPiggyUpdateChecker\libPiggyUpdateChecker;

use Vecnavium\FormsUI\FormsUI;

use AndreasHGK\EasyKits\EasyKits;

class DataManager{

    /** @var Config $config */
    public static Config $config;

    /** @var Config $lang */
    public static Config $lang;

    /** @var Config $cmd */
    public static Config $cmd;

    public const VERSIONS = [
        "config" => 8,
        "commands" => 4,
        "lang" => 10,
    ];

    public const CONFIG = "config.yml";
    public const LANG = "lang.yml";
    public const KITS = "kits.yml";
    public const COMMANDS = "commands.yml";
    public const COOLDOWN = "cooldown.json";
    public const CATEGORIES = "categories.yml";

    /**
     * @var DataManager
     */
    public static $instance = null;

    /**
     * @var Config[]
     */
    public static $memory = [];

    /**
     * @param string $file
     * @param string $key
     * @param bool $default
     * @return mixed
     */
    public static function getKey(string $file, string $key, $default = false): mixed{
        return self::get($file)->get($key, $default);
    }

    /**
     * @param string $file
     * @param boolean $keepLoaded
     * @return Config
     */
    public static function get(string $file, bool $keepLoaded = true): Config{
        if(self::isLoaded($file)) return self::$memory[$file];
        return self::load($file, $keepLoaded);
    }

    /**
     * @param string $file
     * @param boolean $keepLoaded
     * @return Config
     */
    public static function load(string $file, bool $keepLoaded = true): Config{
        $data = self::getFile($file);
        if($keepLoaded) {
            self::$memory[$file] = $data;
        }
        return $data;
    }

    /**
     * @param string $file
     * @param boolean $save
     * @return boolean
     */
    public static function reload(string $file, bool $save = false): bool{
        if(!self::isLoaded($file)) return false;
        if($save) self::get($file)->save();
        self::get($file)->reload();
        return true;
    }

    /**
     * @param string $file
     * @return boolean
     */
    public static function unload(string $file): bool{
        if(!self::isLoaded($file)) return false;
        self::save($file);
        unset(self::$memory[$file]);
        return true;
    }

    /**
     * @param string $file
     * @return boolean
     */
    public static function isLoaded(string $file): bool{
        return isset(self::$memory[$file]);
    }

    /**
     * @param string $file
     * @return boolean
     */
    public static function save(string $file): bool{
        if(!self::isLoaded($file)) return false;
        self::$memory[$file]->save();
        return true;
    }

    /**
     * @param string $file
     * @return Config
     */
    public static function getFile(string $file): Config{
        return new Config(EasyKits::get()->getDataFolder() . $file);
    }

    /**
     * @param string $file
     * @return void
     */
    public static function deleteFile(string $file): void{
        unlink(EasyKits::get()->getDataFolder() . $file);
    }

    /**
     * @param string $file
     * @return boolean
     */
    public static function exists(string $file): bool{
        return file_exists(EasyKits::get()->getDataFolder() . $file);
    }

    /**
     * @return void
     */
    public static function loadFiles(): void{
        EasyKits::get()->saveResource(self::CONFIG);
        EasyKits::get()->saveResource(self::LANG);
        EasyKits::get()->saveResource(self::COMMANDS);
        EasyKits::get()->saveResource(self::KITS);
        EasyKits::get()->saveResource(self::CATEGORIES);
        self::$config = new Config(EasyKits::get()->getDataFolder(). self::CONFIG);
        self::$lang = new Config(EasyKits::get()->getDataFolder(). self::LANG);
        self::$cmd = new Config(EasyKits::get()->getDataFolder(). self::COMMANDS);
    }
    /**
     * @return void
     */
    public static function loadCheck(): void{
        # CONFIG
        if((!self::$config->exists("version")) || (self::$config->get("version") != self::VERSIONS["config"])){
            rename(EasyKits::get()->getDataFolder() . self::CONFIG, EasyKits::get()->getDataFolder() . "config_old.yml");
            EasyKits::get()->saveResource(self::CONFIG);
            EasyKits::get()->getLogger()->critical("Your configuration file is outdated.");
            EasyKits::get()->getLogger()->notice("Your old configuration has been saved as config_old.yml and a new configuration file has been generated. Please update accordingly.");
        }
        # LANG
        if((!self::$lang->exists("version")) || (self::$lang->get("version") != self::VERSIONS["lang"])){
            rename(EasyKits::get()->getDataFolder() . self::LANG, EasyKits::get()->getDataFolder() . "lang_old.yml");
            EasyKits::get()->saveResource(self::LANG);
            EasyKits::get()->getLogger()->critical("Your configuration file is outdated.");
            EasyKits::get()->getLogger()->notice("Your old configuration has been saved as lang_old.yml and a new configuration file has been generated. Please update accordingly.");
        }
        # COMMANDS
        if((!self::$cmd->exists("version")) || (self::$cmd->get("version") != self::VERSIONS["commands"])){
            rename(EasyKits::get()->getDataFolder() . self::COMMANDS, EasyKits::get()->getDataFolder() . "commands_old.yml");
            EasyKits::get()->saveResource(self::COMMANDS);
            EasyKits::get()->getLogger()->critical("Your configuration file is outdated.");
            EasyKits::get()->getLogger()->notice("Your old configuration has been saved as commands_old.yml and a new configuration file has been generated. Please update accordingly.");
        }
        # EXTRA
        if(EasyKits::get()->saveResource(self::KITS)) EasyKits::get()->getLogger()->debug("creating " . self::KITS);
        if(EasyKits::get()->saveResource(self::CATEGORIES)) EasyKits::get()->getLogger()->debug("creating " . self::CATEGORIES);
        self::get(self::KITS);
        self::get(self::CATEGORIES);
        self::get(self::COOLDOWN);
    }

    /**
     * @return void
     */
    public static function loadVirions(): void{
        foreach([
            "FormsUI" => FormsUI::class,
            "InvMenu" => InvMenu::class,
            "libPiggyUpdateChecker" => libPiggyUpdateChecker::class
            ] as $virion => $class
        ){
            if(!class_exists($class)){
                EasyKits::get()->getLogger()->error($virion . " virion not found. Please download EasyKits from Poggit-CI or use DEVirion (not recommended).");
                EasyKits::get()->getServer()->getPluginManager()->disablePlugin(EasyKits::get());
                return;
            }
        }
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register(EasyKits::get());
        }
        # Update
        libPiggyUpdateChecker::init(EasyKits::get());
    }

    /**
     * @return void
     */
    public static function updateAllConfigs() : void {
        $cfgs = [
            self::CONFIG,
            self::COMMANDS,
            self::LANG,
        ];
        foreach($cfgs as $cfg) {
            self::updateConfig($cfg);
        }
    }

    /**
     * @param string $file
     * @return void
     */
    public static function updateConfig(string $file) : void {
        $cfg = self::get($file)->getAll();

        $reflect = new ReflectionProperty(PluginBase::class, "file");
        $reflect->setAccessible(true);
        $Pfile = $reflect->getValue(EasyKits::get());

        $filename = rtrim(str_replace("\\", "/", $file), "/");
        if(file_exists($Pfile . "resources/" . $filename)) {
            $resource = new Config($Pfile . "resources/" . $filename);
            $cfgResource = $resource->getAll();
        }
        $count = 0;
        foreach($cfgResource as $key => $value) {
            if(!isset($cfg[$key])) {
                $count++;
                $cfg[$key] = $value;
            }
        }
        if($count > 0) {
            DataManager::get($file)->setAll($cfg);
            DataManager::save($file);
            EasyKits::get()->getLogger()->notice("Auto updated " . $count . " keys in " . $file);
        }
    }
    private function __construct() {
    }
}
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

namespace AndreasHGK\EasyKits\command;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

use AndreasHGK\EasyKits\EasyKits;
use AndreasHGK\EasyKits\manager\DataManager;

abstract class EKExecutor implements CommandExecutor{

    protected $name;
    protected $desc;
    protected $aliases = [];
    protected $permission;
    protected $usage;

    /**
     * @param string $commandName
     * @return void
     */
    protected function setDataFromConfig(string $commandName): void{
        $commandData = DataManager::getKey(DataManager::COMMANDS, $commandName);
        $this->name = array_shift($commandData["labels"]);
        if(isset($commandData["labels"])) $this->aliases = $commandData["labels"];
        $this->desc = $commandData["description"];
        $this->usage = $commandData["usage"];
        $this->permission = EasyKits::PERM_ROOT . "command." . $commandName;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDesc(): string{
        return $this->desc;
    }

    /**
     * @return array
     */
    public function getAliases(): array{
        return $this->aliases;
    }

    /**
     * @return string
     */
    public function getPermission(): string{
        return $this->permission;
    }

    /**
     * @return string
     */
    public function getUsage(): string{
        return $this->usage;
    }

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return boolean
     */
    abstract public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool;

}

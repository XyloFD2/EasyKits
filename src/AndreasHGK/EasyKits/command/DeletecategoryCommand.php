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

use pocketmine\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use AndreasHGK\EasyKits\ui\DeletecategoryForm;
use AndreasHGK\EasyKits\utils\LangUtils;

class DeletecategoryCommand extends EKExecutor{

    public function __construct() {
        $this->setDataFromConfig("deletecategory");

    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if(!$sender instanceof Player){
            $sender->sendMessage(LangUtils::getMessage("sender-not-player"));
            return true;
        }
        DeletecategoryForm::sendTo($sender);
        return true;
    }

}

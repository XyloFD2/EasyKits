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

namespace AndreasHGK\EasyKits\ui;

use AndreasHGK\EasyKits\utils\LangUtils;
use AndreasHGK\EasyKits\manager\DataManager;
use pocketmine\player\Player;
use pocketmine\Server;

use Vecnavium\FormsUI\SimpleForm;

class GivekitPlayerSelectForm{

    /**
     * @param Player $player
     * @return void
     */
    public static function sendTo(Player $player): void{

        $ui = new SimpleForm(function (Player $player, $data) {
            if($data === null) {
                $player->sendMessage(LangUtils::getMessage("givekit-cancelled"));
                return;
            }
            $target = Server::getInstance()->getPlayerExact($data);
            if($target === null) {
                $player->sendMessage(LangUtils::getMessage("givekit-player-not-found"));
                return;
            }
            GivekitKitSelectForm::sendTo($player, $target);
        });

        $ui->setTitle(LangUtils::getMessage("givekit-title"));
        $ui->setContent(LangUtils::getMessage("givekit-playerselect-text"));

        $give = DataManager::$config->getNested("Form.image.givekit-playerselect-url");
        $giveURL = str_starts_with($give, "http://") || str_starts_with($give, "https://");
        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $ui->addButton(LangUtils::getMessage("givekit-playerselect-format", true, ["{PLAYER}" => $onlinePlayer->getName()]), $giveURL ? 1:0, $give, $onlinePlayer->getName());
        }
        $player->sendForm($ui);
    }

}
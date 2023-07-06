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

use pocketmine\player\Player;

use Vecnavium\FormsUI\CustomForm;

use AndreasHGK\EasyKits\manager\KitManager;
use AndreasHGK\EasyKits\utils\LangUtils;

class DeletekitForm{

    /**
     * @param Player $player
     * @return void
     */
    public static function sendTo(Player $player): void{
        $kits = [];
        foreach(KitManager::getAll() as $kit) {
            $kits[] = $kit->getName();
        }

        $ui = new CustomForm(function (Player $player, $data) use ($kits) {
            if(is_null($data)){
                $player->sendMessage(LangUtils::getMessage("deletekit-cancelled"));
                return;
            }
            if(!isset($data["kit"])) {
                $player->sendMessage(LangUtils::getMessage("deletekit-empty"));
                return;
            }
            if(!KitManager::exists($kits[$data["kit"]])) {
                $player->sendMessage(LangUtils::getMessage("deletekit-not-found"));
                return;
            }
            KitManager::remove(KitManager::get($kits[$data["kit"]]));
            $player->sendMessage(LangUtils::getMessage("deletekit-success", true, ["{NAME}" => $kits[$data["kit"]]]));
            return;
        });
        $ui->setTitle(LangUtils::getMessage("deletekit-title"));
        $ui->addLabel(LangUtils::getMessage("deletekit-text"));
        $ui->addDropdown(LangUtils::getMessage("deletekit-select"), $kits, null, "kit");
        $player->sendForm($ui);
    }

}
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

use AndreasHGK\EasyKits\manager\KitManager;
use AndreasHGK\EasyKits\utils\LangUtils;

use pocketmine\player\Player;

use Vecnavium\FormsUI\CustomForm;

class EditkitSelectForm {

    public static function sendTo(Player $player) : void {
        $kits = [];
        foreach(KitManager::getAll() as $kit) {
            $kits[] = $kit->getName();
        }

        $ui = new CustomForm(function (Player $player, $data) use ($kits) {
            if($data === null) {
                $player->sendMessage(LangUtils::getMessage("editkit-cancelled"));
                return;
            }
            if(!isset($data["kit"])) {
                $player->sendMessage(LangUtils::getMessage("editkit-empty"));
                return;
            }
            if(!KitManager::exists($kits[$data["kit"]])) {
                $player->sendMessage(LangUtils::getMessage("editkit-not-found"));
                return;
            }
            EditkitMainForm::sendTo($player, KitManager::get($kits[$data["kit"]]));
            return;
        });
        $ui->setTitle(LangUtils::getMessage("editkit-title"));
        $ui->addLabel(LangUtils::getMessage("editkit-select-text"));
        $ui->addDropdown(LangUtils::getMessage("editkit-select"), $kits, null, "kit");
        $player->sendForm($ui);
    }

}
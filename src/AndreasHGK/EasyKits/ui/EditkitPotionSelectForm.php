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

use AndreasHGK\EasyKits\Kit;
use AndreasHGK\EasyKits\utils\LangUtils;

use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\player\Player;
use pocketmine\Server;

use Vecnavium\FormsUI\SimpleForm;


class EditkitPotionSelectForm {

    public static function sendTo(Player $player, Kit $kit) : void {
        $ui = new SimpleForm(function (Player $player, $data) use ($kit) {
            if($data === null) {
                EditkitMainForm::sendTo($player, $kit);
                return;
            }

            EditkitPotionForm::sendTo($player, $kit, $data+1);
        });
        $ui->setTitle(LangUtils::getMessage("editkit-title"));
        $ui->setContent(LangUtils::getMessage("editkit-potionselect-text"));
        $effects = [];
        for($i=1; $i <= 26; $i++){
            $effects[] = EffectIdMap::getInstance()->fromId($i)->getName();
        }
        foreach($effects as $effect){
            $ui->addButton(LangUtils::getMessage("editkit-potionselect-button", true, ["{POTION}" => Server::getInstance()->getLanguage()->translate($effect)]));
        }
        $player->sendForm($ui);
    }

}
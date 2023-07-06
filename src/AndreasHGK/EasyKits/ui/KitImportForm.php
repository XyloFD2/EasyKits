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

use AndreasHGK\EasyKits\importer\AdvancedKitsImporter;
use AndreasHGK\EasyKits\importer\KitsPlusImporter;
use AndreasHGK\EasyKits\importer\KitUIImporter;
use AndreasHGK\EasyKits\utils\LangUtils;
use Vecnavium\FormsUI\CustomForm;
use pocketmine\player\Player;

class KitImportForm {

    /**
     * @param Player $player
     * @return void
     */
    public static function sendTo(Player $player): void{
        $ui = new CustomForm(function (Player $player, $data){
            if($data === null) {
                $player->sendMessage(LangUtils::getMessage("ekimport-cancelled"));
                return;
            }
            switch($data["dropdown"]) {
                case 0:
                    $name = "AdvancedKits";
                    if(!AdvancedKitsImporter::isPluginLoaded()) {
                        $player->sendMessage(LangUtils::getMessage("ekimport-not-loaded"));
                        return;
                    }
                    $return = AdvancedKitsImporter::ImportAll();
                    break;
                case 1:
                    $name = "KitUI";
                    if(!KitUIImporter::isPluginLoaded()) {
                        $player->sendMessage(LangUtils::getMessage("ekimport-not-loaded"));
                        return;
                    }
                    $return = KitUIImporter::ImportAll();
                    break;
                case 2:
                    $name = "KitsPlus";
                    if(!KitsPlusImporter::isPluginLoaded()) {
                        $player->sendMessage(LangUtils::getMessage("ekimport-not-loaded"));
                        return;
                    }
                    $return = KitsPlusImporter::ImportAll();
                    break;
                default:
                    $player->sendMessage(LangUtils::getMessage("ekimport-not-loaded"));
                    return;
                    break;
            }
            $success = 0;
            $failed = 0;
            foreach($return as $bool) {
                if($bool) $success++;
                else $failed++;
            }
            $player->sendMessage(LangUtils::getMessage("ekimport-success", true, ["{PLUGIN}" => $name, "{SUCCESS}" => $success, "{FAILED}" => $failed]));
            return;
        });
        $ui->setTitle(LangUtils::getMessage("ekimport-title"));
        $ui->addLabel(LangUtils::getMessage("ekimport-text"));
        $ui->addDropdown(LangUtils::getMessage("ekimport-select"), ["AdvancedKits", "KitUI", "KitsPlus",], null, "dropdown");
        $player->sendForm($ui);
    }

}
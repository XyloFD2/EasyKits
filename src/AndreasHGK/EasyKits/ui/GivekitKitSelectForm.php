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
use AndreasHGK\EasyKits\utils\KitException;
use AndreasHGK\EasyKits\utils\LangUtils;
use AndreasHGK\EasyKits\utils\TryClaim;

use AndreasHGK\EasyKits\manager\DataManager;
use pocketmine\player\Player;

use Vecnavium\FormsUI\SimpleForm;

class GivekitKitSelectForm{

    /**
     * @param Player $player
     * @param Player $target
     * @return void
     */
    public static function sendTo(Player $player, Player $target): void{

        $ui = new SimpleForm(function (Player $player, $data) use ($target) {
            if($data === null) {
                $player->sendMessage(LangUtils::getMessage("givekit-cancelled"));
                return;
            }
            if(!KitManager::exists($data)) {
                $player->sendMessage(LangUtils::getMessage("givekit-kit-not-found"));
                return;
            }
            try {
                $kit = KitManager::get($data);
                TryClaim::ForceClaim($target, $kit);
                $player->sendMessage(LangUtils::getMessage("givekit-success", true, ["{KIT}" => $kit->getName(), "{PLAYER}" => $target->getName()]));
            } catch(KitException $e) {
                switch($e->getCode()) {
                    case 3:
                        $player->sendMessage(LangUtils::getMessage("givekit-insufficient-space"));
                        break;
                    default:
                        $player->sendMessage(LangUtils::getMessage("unknown-exception"));
                        break;
                }
            }
        });

        $ui->setTitle(LangUtils::getMessage("givekit-title"));
        $ui->setContent(LangUtils::getMessage("givekit-kitselect-text"));

        $give = DataManager::$config->getNested("Form.image.givekit-kitselect-url");
        $giveURL = str_starts_with($give, "http://") || str_starts_with($give, "https://");
        foreach(KitManager::getAll() as $kit) {
            $ui->addButton(LangUtils::getMessage("givekit-kitselect-format", true, ["{NAME}" => $kit->getName()]), $giveURL ? 1:0, $give, $kit->getName());
        }
        $player->sendForm($ui);
    }

}

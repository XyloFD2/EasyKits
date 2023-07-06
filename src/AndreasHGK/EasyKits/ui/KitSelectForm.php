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

use AndreasHGK\EasyKits\Category;
use AndreasHGK\EasyKits\manager\CooldownManager;
use AndreasHGK\EasyKits\manager\DataManager;
use AndreasHGK\EasyKits\manager\KitManager;
use AndreasHGK\EasyKits\utils\LangUtils;
use AndreasHGK\EasyKits\utils\TimeUtils;
use AndreasHGK\EasyKits\utils\TryClaim;
use Vecnavium\FormsUI\SimpleForm;
use pocketmine\player\Player;

class KitSelectForm {

    /**
     * @param Player $player
     * @param Category|null $category
     * @return void
     */
    public static function sendTo(Player $player, Category $category = null): void{

        $ui = new SimpleForm(function (Player $player, $data) use ($category) {
            if($data === null) {
                if($category !== null) CategorySelectForm::sendTo($player);
                return;
            }
            if(!KitManager::exists($data)) {
                $player->sendMessage(LangUtils::getMessage("kit-not-found"));
                return;
            }
            TryClaim::tryClaim(KitManager::get($data), $player);
        });

        $free = DataManager::$config->getNested("Form.image.kit-free-url");
        $freeURL = str_starts_with($free, "http://") || str_starts_with($free, "https://");

        $lock = DataManager::$config->getNested("Form.image.kit-lock-url");
        $lockURL = str_starts_with($lock, "http://") || str_starts_with($lock, "https://");

        $cool = DataManager::$config->getNested("Form.image.kit-cooldown-url");
        $coolURL = str_starts_with($cool, "http://") || str_starts_with($cool, "https://");

        $priced = DataManager::$config->getNested("Form.image.kit-priced-url");
        $pricedURL = str_starts_with($priced, "http://") || str_starts_with($priced, "https://");

        if($category !== null) {
            $ui->setTitle(LangUtils::getMessage("category-title", true, ["{NAME}" => $category->getName()]));
            $ui->setContent(LangUtils::getMessage("category-text"));

            foreach($category->getPermittedKitsFor($player) as $kit) {
                if(CooldownManager::hasKitCooldown($kit, $player)) {
                    $ui->addButton(LangUtils::getMessage("kit-cooldown-format", true, ["{NAME}" => $kit->getName(), "{PRICE}" => $kit->getPrice(), "{COOLDOWN}" => $timeString = TimeUtils::intToTimeString(CooldownManager::getKitCooldown($kit, $player))]), $coolURL ? 1:0, $cool, $kit->getName());
                } elseif($kit->getPrice() > 0) {
                    $ui->addButton(LangUtils::getMessage("kit-available-priced-format", true, ["{NAME}" => $kit->getName(), "{PRICE}" => $kit->getPrice()]), $pricedURL ? 1:0, $priced, $kit->getName());
                } else {
                    $ui->addButton(LangUtils::getMessage("kit-available-free-format", true, ["{NAME}" => $kit->getName()]), $freeURL ? 1:0, $free, $kit->getName());
                }
            }
            if(DataManager::getKey(DataManager::CONFIG, "show-locked")) {
                foreach($category->getKits() as $kit) {
                    if($kit->hasPermission($player)) continue;
                    $ui->addButton(LangUtils::getMessage("kit-locked-format", true, ["{NAME}" => $kit->getName(), "{PRICE}" => $kit->getPrice()]), $lockURL ? 1:0, $lock, $kit->getName());
                }
            }
        } else {
            $ui->setTitle(LangUtils::getMessage("kit-title"));
            $ui->setContent(LangUtils::getMessage("kit-text"));

            foreach(KitManager::getPermittedKitsFor($player) as $kit) {
                if(CooldownManager::hasKitCooldown($kit, $player)) {
                    $ui->addButton(LangUtils::getMessage("kit-cooldown-format", true, ["{NAME}" => $kit->getName(), "{PRICE}" => $kit->getPrice(), "{COOLDOWN}" => $timeString = TimeUtils::intToTimeString(CooldownManager::getKitCooldown($kit, $player))]), $coolURL ? 1:0, $cool, $kit->getName());
                } elseif($kit->getPrice() > 0) {
                    $ui->addButton(LangUtils::getMessage("kit-available-priced-format", true, ["{NAME}" => $kit->getName(), "{PRICE}" => $kit->getPrice()]), $pricedURL ? 1:0, $priced, $kit->getName());
                } else {
                    $ui->addButton(LangUtils::getMessage("kit-available-free-format", true, ["{NAME}" => $kit->getName()]), $freeURL ? 1:0, $free, $kit->getName());
                }
            }
            // locked kits should be displayed below unlocked ones
            if(DataManager::getKey(DataManager::CONFIG, "show-locked")) {

                foreach(KitManager::getAll() as $kit) {
                    if($kit->hasPermission($player)) continue;
                    $ui->addButton(LangUtils::getMessage("kit-locked-format", true, ["{NAME}" => $kit->getName(), "{PRICE}" => $kit->getPrice()]), $lockURL ? 1:0, $lock, $kit->getName());
                }
            }
        }
        $player->sendForm($ui);
    }
}
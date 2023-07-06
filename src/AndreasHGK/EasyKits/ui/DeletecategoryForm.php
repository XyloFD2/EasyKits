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

use AndreasHGK\EasyKits\manager\CategoryManager;
use AndreasHGK\EasyKits\utils\LangUtils;

class DeletecategoryForm{

    /**
     * @param Player $player
     * @return void
     */
    public static function sendTo(Player $player): void{
        $categories = [];
        foreach(CategoryManager::getAll() as $category) {
            $categories[] = $category->getName();
        }

        $ui = new CustomForm(function (Player $player, $data) use ($categories) {
            if($data === null) {
                $player->sendMessage(LangUtils::getMessage("deletecategory-cancelled"));
                return;
            }
            if(!isset($data["category"])) {
                $player->sendMessage(LangUtils::getMessage("deletecategory-empty"));
                return;
            }
            if(!CategoryManager::exists($categories[$data["category"]])) {
                $player->sendMessage(LangUtils::getMessage("deletecategory-not-found"));
                return;
            }
            if(CategoryManager::remove(CategoryManager::get($categories[$data["category"]]))) {
                $player->sendMessage(LangUtils::getMessage("deletecategory-success", true, ["{NAME}" => $categories[$data["category"]]]));
                CategoryManager::saveAll();
            }
            return;
        });
        $ui->setTitle(LangUtils::getMessage("deletecategory-title"));
        $ui->addLabel(LangUtils::getMessage("deletecategory-text"));
        $ui->addDropdown(LangUtils::getMessage("deletecategory-select"), $categories, null, "category");
        $player->sendForm($ui);
    }

}
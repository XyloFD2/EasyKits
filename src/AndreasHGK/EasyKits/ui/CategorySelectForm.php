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

use Vecnavium\FormsUI\SimpleForm;

use AndreasHGK\EasyKits\event\CategorySelectEvent;
use AndreasHGK\EasyKits\manager\CategoryManager;
use AndreasHGK\EasyKits\manager\DataManager;
use AndreasHGK\EasyKits\utils\LangUtils;

class CategorySelectForm {

    /**
     * @param Player $player
     * @return void
     */
    public static function sendTo(Player $player): void{
        if(empty(CategoryManager::getPermittedCategoriesFor($player))) {
            $player->sendMessage(LangUtils::getMessage("no-categories"));
            return;
        }

        $ui = new SimpleForm(function(Player $player, $data){
            if(is_null($data)){
                return;
            }
            $category = CategoryManager::get($data);
            if(!isset($category)) {
                $player->sendMessage(LangUtils::getMessage("category-not-found"));
                return;
            }
            if(!$category->hasPermission($player)) {
                $player->sendMessage(LangUtils::getMessage("category-no-permission"));
                return;
            }

            $event = new CategorySelectEvent($player, $category);
            $event->call();

            if($event->isCancelled()) return;

            if(empty($event->getCategory()->getPermittedKitsFor($player))) {
                $player->sendMessage(LangUtils::getMessage("category-empty"));
                return;
            }

            KitSelectForm::sendTo($event->getPlayer(), $event->getCategory());
        });
        $ui->setTitle(LangUtils::getMessage("category-select-title"));
        $ui->setContent(LangUtils::getMessage("category-select-text"));
        # IMG
        $unlock = DataManager::$config->getNested("Form.image.category-unlocked-url");
        $unlockURL = str_starts_with($unlock, "http://") || str_starts_with($unlock, "https://");
        $lock = DataManager::$config->getNested("Form.image.category-locked-url");
        $lockURL = str_starts_with($lock, "http://") || str_starts_with($lock, "https://");
        foreach(CategoryManager::getAll() as $category){
            if($category->hasPermission($player)){
                $ui->addButton(LangUtils::getMessage("category-unlocked-format", true, ["{NAME}" => $category->getName()]), $unlockURL ? 1 : 0, $unlock, $category->getName());
            }elseif(DataManager::getKey(DataManager::CONFIG, "show-locked-categories")) {
                $ui->addButton(LangUtils::getMessage("category-locked-format", true, ["{NAME}" => $category->getName()]), $lockURL ? 1 : 0, $lock, $category->getName());
            }
        }
        $player->sendForm($ui);
    }

}
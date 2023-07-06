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

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;

use pocketmine\inventory\Inventory;

use pocketmine\item\VanillaItems;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;

use AndreasHGK\EasyKits\manager\KitManager;
use AndreasHGK\EasyKits\utils\LangUtils;
use AndreasHGK\EasyKits\Kit;

class EditKitItemInventory {

    public static function sendTo(Player $player, Kit $kit) : void {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName(LangUtils::getMessage("editkit-items-title", true, ["{NAME}" => $kit->getName()]));
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) use($kit): void{
            $items = [];
            for($i = 0; $i < 36; $i++){
                $item = $inventory->getItem($i);
                if($item->getTypeId() !== VanillaItems::AIR()->getTypeId()){
                    $items[$i] = $item;
                }
            }
            $armor = [];
            
            $armorPiece = $inventory->getItem(47);
            if($armorPiece->getTypeId() !== VanillaItems::AIR()->getTypeId()){
                $armor[3] = $armorPiece;
            }
            
            $armorPiece = $inventory->getItem(48);
            if($armorPiece->getTypeId() !== VanillaItems::AIR()->getTypeId()){
                $armor[2] = $armorPiece;
            }
            
            $armorPiece = $inventory->getItem(50);
            if($armorPiece->getTypeId() !== VanillaItems::AIR()->getTypeId()){
                $armor[1] = $armorPiece;
            }
            
            $armorPiece = $inventory->getItem(51);
            if($armorPiece->getTypeId() !== VanillaItems::AIR()->getTypeId()){
                $armor[0] = $armorPiece;
            }
            
            $new = clone $kit;
            $new->setItems($items);
            $new->setArmor($armor);
            
            if($kit->getItems() === $items && $kit->getArmor() === $armor){
                EditkitMainForm::sendTo($player, $kit);
            }
            
            if(KitManager::update($kit, $new)){
                $player->sendMessage(LangUtils::getMessage("editkit-items-succes", true, ["{COUNT}" => count($items) + count($armor), "{NAME}" => $kit->getName()]));
                EditkitMainForm::sendTo($player, KitManager::get($kit->getName()));
            }            
        });
        $menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
            if($transaction->getItemClicked()->getNamedTag()->getTag("immovable") == null){
                return $transaction->continue();
            }
            return $transaction->discard();
        });
        $menu->getInventory()->setContents($kit->getItems());
        for($i = 36; $i < 54; $i++) {
            switch($i) {
                case 42:
                    $item = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem();
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $item->setLore([LangUtils::getMessage("editkit-items-helmet")]);
                    $menu->getInventory()->setItem($i, $item);
                    break;
                case 41:
                    $item = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem();
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $item->setLore([LangUtils::getMessage("editkit-items-chestplate")]);
                    $menu->getInventory()->setItem($i, $item);
                    break;
                case 39:
                    $item = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem();
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $item->setLore([LangUtils::getMessage("editkit-items-leggings")]);
                    $menu->getInventory()->setItem($i, $item);
                    break;
                case 38:
                    $item = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem();
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $item->setLore([LangUtils::getMessage("editkit-items-boots")]);
                    $menu->getInventory()->setItem($i, $item);
                    break;
                case 51:
                    $menu->getInventory()->setItem($i, $kit->getArmor()[0] ?? VanillaItems::AIR());
                    break;
                case 50:
                    $menu->getInventory()->setItem($i, $kit->getArmor()[1] ?? VanillaItems::AIR());
                    break;
                case 48:
                    $menu->getInventory()->setItem($i, $kit->getArmor()[2] ?? VanillaItems::AIR());
                    break;
                case 47:
                    $menu->getInventory()->setItem($i, $kit->getArmor()[3] ?? VanillaItems::AIR());
                    break;
                default:
                    $item = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem();
                    $item->setCustomName(LangUtils::getMessage("editkit-items-lockedname"));
                    $item->setNamedTag(CompoundTag::create()->setTag("immovable", new StringTag("allowed")));
                    $menu->getInventory()->setItem($i, $item);
                    break;
            }
        }
        $menu->send($player);
    }
}
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

namespace AndreasHGK\EasyKits\utils;

use pocketmine\data\bedrock\EnchantmentIdMap;

use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\utils\TextFormat;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;

use AndreasHGK\EasyKits\customenchants\PiggyCustomEnchantsLoader;
use AndreasHGK\EasyKits\manager\DataManager;

abstract class ItemUtils {

    public const ITEM_FORMAT = [
        "id" => 1,
        "damage" => 0,
        "count" => 1,
        "display_name" => "",
        "lore" => [

        ],
        "enchants" => [

        ],
    ];

    /**
     * @param array $itemData
     * @return Item
     */
    public static function dataToItem(array $itemData): Item{
        switch(strtolower($itemData["format"] ?? "")) {
            case "nbt":
                if(isset($itemData["id"]) && is_int($itemData["id"])){
                    $item = Item::legacyJsonDeserialize($itemData);
                }else{
                    $item = LegacyUtils::legacyStringJsonDeserialize($itemData);
                }
                return $item;
            default:
                if(is_int($itemData["id"])){
                    $item = LegacyStringToItemParser::getInstance()->parse($itemData["id"].":".(isset($itemData["damage"]) ? $itemData["damage"] : 0))->setCount($itemData["count"] ?? 1);
                }else{
                    $item = StringToItemParser::getInstance()->parse($itemData["id"])->setCount($itemData["count"] ?? 1);
                }
                if(isset($itemData["enchants"])) {
                    foreach($itemData["enchants"] as $ename => $level){
                        $ench = EnchantmentIdMap::getInstance()->fromId((int)$ename);
                        if(PiggyCustomEnchantsLoader::isPluginLoaded() && is_null($ench)){
                            if(!PiggyCustomEnchantsLoader::isNewVersion()) $ench = CustomEnchants::getEnchantment((int)$ename);
                            else $ench = CustomEnchantManager::getEnchantment((int)$ename);

                        }
                        if(is_null($ench)) continue;
                        if(!PiggyCustomEnchantsLoader::isNewVersion() && $ench instanceof CustomEnchants){
                            PiggyCustomEnchantsLoader::getPlugin()->addEnchantment($item, $ench->getName(), $level);
                        }else{
                            $item->addEnchantment(new EnchantmentInstance($ench, $level));
                        }
                    }
                }
                if(isset($itemData["display_name"])) $item->setCustomName(TextFormat::colorize($itemData["display_name"]));
                if(isset($itemData["lore"])) {
                    $lore = [];
                    foreach($itemData["lore"] as $key => $ilore) {
                        $lore[$key] = TextFormat::colorize($ilore);
                    }
                    $item->setLore($lore);
                }
                return $item;

        }
    }

    /**
     * @param Item $item
     * @return array
     */
    public static function itemToData(Item $item): array{
        $format = DataManager::getKey(DataManager::CONFIG, "item-format");
        switch(strtolower($format)) {
            case "nbt":
                $itemData = LegacyUtils::jsonSerialize($item);
                return $itemData;
            default:
                /** @var StringToItemParser $serialized */
                $serialized = StringToItemParser::getInstance();
                $itemData = self::ITEM_FORMAT;
                $itemData["id"] = $serialized->lookupAliases($item)[0];
                $itemData["count"] = $item->getCount();
                if($item->hasCustomName()) {
                    $itemData["display_name"] = $item->getCustomName();
                }else{
                    unset($itemData["display_name"]);
                }
                if($item->getLore() !== []) {
                    $itemData["lore"] = $item->getLore();
                }else{
                    unset($itemData["lore"]);
                }
                if($item->hasEnchantments()) {
                    foreach($item->getEnchantments() as $enchantment) {
                        $itemData["enchants"][(string)EnchantmentIdMap::getInstance()->toId($enchantment->getType())] = $enchantment->getLevel();
                    }
                } else {
                    unset($itemData["enchants"]);
                }
                return $itemData;
        }
    }
}
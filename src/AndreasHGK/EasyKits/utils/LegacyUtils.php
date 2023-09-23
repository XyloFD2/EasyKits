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

use pocketmine\item\Item;

use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

class LegacyUtils{
    
    /**
     * @param Item $item
     * @return array
     */
    public static function jsonSerialize(Item $item): array{
        $itemData["nbt_b64"] = base64_encode((new LittleEndianNbtSerializer())->write(new TreeRoot($item->nbtSerialize())));
        $itemData["format"] = "nbt";
        return $itemData;
	}

    /**
     * @param array $data
     * @return Item
     */
    public static function legacyStringJsonDeserialize(array $data): Item{
        $data = base64_decode($data["nbt_b64"]);
        $item = (new LittleEndianNbtSerializer())->read($data);
        return Item::nbtDeserialize($item->mustGetCompoundTag());
	}
}
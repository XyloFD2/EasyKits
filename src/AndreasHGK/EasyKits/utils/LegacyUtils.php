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

use pocketmine\data\bedrock\item\ItemTypeDeserializeException;
use pocketmine\data\SavedDataLoadingException;

use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;

use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

use pocketmine\world\format\io\GlobalItemDataHandlers;

class LegacyUtils{
    
    /**
     * @param Item $item
     * @return array
     */
    public static function jsonSerialize(Item $item): array{
        /** @var StringToItemParser $itemId */
        $itemId = StringToItemParser::getInstance();
        $serialized = GlobalItemDataHandlers::getSerializer()->serializeType($item);
		$data = [
			"id" => $itemId->lookupAliases($item)[0]
		];
		if($item->getCount() !== 1){
			$data["count"] = $item->getCount();
		}
        //if($item instanceof Durable){
        if($serialized->getMeta() !== 0){
            $data["damage"] = $item instanceof Durable ? $item->getDamage() : $serialized->getMeta();
        }
       // }
		if($item->hasNamedTag()){
			$data["nbt_b64"] = base64_encode((new LittleEndianNbtSerializer())->write(new TreeRoot($item->getNamedTag())));
		}
		return $data;
	}

    /**
     * @param array $data
     * @return Item
     */
    public static function legacyStringJsonDeserialize(array $data): Item{
        $nbt = "";

        //Backwards compatibility
        if(isset($data["nbt"])) {
            $nbt = $data["nbt"];
        }elseif(isset($data["nbt_hex"])) {
            $nbt = hex2bin($data["nbt_hex"]);
        }elseif (isset($data["nbt_b64"])) {
            $nbt = base64_decode($data["nbt_b64"], true);
        }
        $itemStackData = GlobalItemDataHandlers::getUpgrader()->upgradeItemTypeDataString($data['id'], $data['damage'] ?? 0, $data['count'] ?? 1,
            $nbt !== "" ? (new LittleEndianNbtSerializer())->read($nbt)->mustGetCompoundTag() : null
        );

        try{
            return GlobalItemDataHandlers::getDeserializer()->deserializeStack($itemStackData);
        }catch(ItemTypeDeserializeException $e){
            throw new SavedDataLoadingException($e->getMessage(), 0, $e);
        }
	}
}
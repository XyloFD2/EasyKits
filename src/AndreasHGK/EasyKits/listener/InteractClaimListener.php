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

namespace AndreasHGK\EasyKits\listener;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerInteractEvent;

use AndreasHGK\EasyKits\manager\KitManager;
use AndreasHGK\EasyKits\utils\TryClaim;

class InteractClaimListener implements Listener{

    /**
     * @param PlayerInteractEvent $ev
     * @return void
     */
    public function onInteract(PlayerInteractEvent $ev): void{
        $item = $ev->getItem();
        if(!is_null($item->getNamedTag()->getTag("ekit"))){
            $item->getNamedTag()->getString("ekit");
            $kitname = $item->getNamedTag()->getString("ekit");
            if(KitManager::exists($kitname)){
                $ev->cancel();
                $player = $ev->getPlayer();
                $kit = KitManager::get($kitname);
                TryClaim::TryChestClaim($player, $item, $kit);
            }
        }
    }
}
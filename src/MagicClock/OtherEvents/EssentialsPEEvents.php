<?php
namespace MagicClock\OtherEvents;

use EssentialsPE\Events\PlayerVanishEvent;
use MagicClock\Loader;
use pocketmine\event\Listener;

class EssentialsPEEvents implements Listener{
    /** @var \MagicClock\Loader  */
    public $plugin;

    public function __construct(Loader $plugin){
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerVanishEvent $event
     */
    public function onPlayerVanish(PlayerVanishEvent $event){
        $player = $event->getPlayer();
        if(!$event->willVanish()){
            foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
                $event->setCancelled(true);
                if(!$this->plugin->isMagicClockEnabled($p)){
                    $p->showPlayer($player);
                }
            }
        }
    }
} 
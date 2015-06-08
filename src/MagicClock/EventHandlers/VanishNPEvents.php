<?php
namespace MagicClock\EventHandlers;

use MagicClock\Loader;
use pocketmine\event\Listener;
use VanishNP\PlayerVanishEvent;

class VanishNPEvents implements Listener{
    /** @var Loader */
    private $plugin;

    public function __construct(Loader $plugin){
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerVanishEvent $event
     *
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onPlayerVanish(PlayerVanishEvent $event){
        if($event->willVanish()){
            foreach($event->getPlayer()->getLevel()->getPlayers() as $p){
                if($this->plugin->isMagicClockEnabled($p)){
                    $event->keepHiddenFor($p);
                }
            }
        }
    }

}
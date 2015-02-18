<?php
namespace MagicClock\EventHandlers;

use EssentialsPE\Events\PlayerVanishEvent;
use MagicClock\Loader;
use pocketmine\event\Listener;

class EssentialsPEEvents implements Listener{
    /** @var Loader  */
    public $plugin;

    public function __construct(Loader $plugin){
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerVanishEvent $event
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
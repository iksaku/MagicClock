<?php
namespace MagicClock;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;

class EventHandler implements Listener{
    /** @var  Loader */
    public $plugin;

    public function __construct(Loader $plugin){
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $this->plugin->players[$player->getName()] = false;
        if($this->plugin->getConfig()->get("enableonjoin") === true){
            $this->plugin->toggleMagicClock($player);
        }
        if(!$player->hasPermission("magicclock.exempt")){
            foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
                if($this->plugin->isMagicClockEnabled($p)){
                    $p->hidePlayer($player);
                }
            }
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onPlayerDamageByPlayer(EntityDamageByEntityEvent $event){
        $victim = $event->getEntity();
        $issuer = $event->getDamager();
        if($victim instanceof Player && $issuer instanceof Player){
            if($this->plugin->isMagicClockEnabled($issuer)){
                $event->setCancelled(true);
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onBlockTouch(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        if($item->getID() == $this->plugin->getConfig()->get("itemID")){
            $this->plugin->toggleMagicClock($player);
        }
    }
} 
<?php
namespace MagicClock;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\protocol\UseItemPacket;
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
        $this->plugin->players[$event->getPlayer()->getName()] = false;
        if($this->plugin->getConfig()->get("enableonjoin") === true){
            $this->plugin->toggleMagicClock($event->getPlayer());
        }
        if(!$event->getPlayer()->hasPermission("magicclock.exempt")){
            foreach($event->getPlayer()->getLevel()->getPlayers() as $p){
                if($this->plugin->isMagicClockEnabled($p)){
                    $p->hidePlayer($event->getPlayer());
                }
            }
        }
    }

    /**
     * @param EntityLevelChangeEvent $event
     */
    public function onEntityLevelChange(EntityLevelChangeEvent $event){
        $player = $event->getEntity();
        $target = $event->getTarget();
        if($player instanceof Player){
            foreach($target->getPlayers() as $p){
                if($this->plugin->isMagicClockEnabled($p) && !$player->hasPermission("magicclock.exempt")){
                    $p->hidePlayer($player);
                }
                if($this->plugin->isMagicClockEnabled($player) && !$p->hasPermission("magicclock.exempt")){
                    $player->hidePlayer($p);
                }
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onPlayerChat(PlayerChatEvent $event){
        if($this->plugin->isChatDisabled() && $this->plugin->isMagicClockEnabled($event->getPlayer()) && !$event->getPlayer()->hasPermission("magicclock.canchat")){
            $event->setCancelled(true);
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onEntityDamageByEntity(EntityDamageEvent $event){
        if($event instanceof EntityDamageByEntityEvent){
            $victim = $event->getEntity();
            $issuer = $event->getDamager();
            if($victim instanceof Player && $issuer instanceof Player){
                if($this->plugin->isMagicClockEnabled($victim) || $this->plugin->isMagicClockEnabled($issuer)){
                    $event->setCancelled(true);
                }
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onBlockTouch(PlayerInteractEvent $event){
        if($event->getItem()->getID() == $this->plugin->getConfig()->get("itemID")){
            $event->setCancelled(true);
            $this->plugin->toggleMagicClock($event->getPlayer());
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onBlockPlace(BlockPlaceEvent $event){
        if($event->getItem()->getID() == $this->plugin->getConfig()->get("itemID")){
            $event->setCancelled(true);
            $this->plugin->toggleMagicClock($event->getPlayer());
        }
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onDataReceive(DataPacketReceiveEvent $event){
        $packet = $event->getPacket();
        if(($packet instanceof UseItemPacket && $packet->face === 0xff) && $packet->item == $this->plugin->getConfig()->get("itemID")){
            $event->setCancelled(true);
            $this->plugin->toggleMagicClock($event->getPlayer());
        }
    }
}

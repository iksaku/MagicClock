<?php
namespace MagicClock;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\plugin\PluginDisableEvent;
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
            foreach($player->getLevel()->getPlayers() as $p){
                if($this->plugin->isMagicClockEnabled($p)){
                    $p->hidePlayer($player);
                }
            }
        }
    }

    /**
     * @param EntityLevelChangeEvent $event
     */
    public function onPlayerLevelChange(EntityLevelChangeEvent $event){
        $player = $event->getEntity();
        $target = $event->getTarget();
        if($player instanceof Player && !$player->hasPermission("magicclock.exempt")){
            foreach($target->getPlayers() as $p){
                if($this->plugin->isMagicClockEnabled($p)){
                    $p->hidePlayer($player);
                }
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onPlayerChat(PlayerChatEvent $event){
        $player = $event->getPlayer();
        if($this->plugin->isChatDisabled() && $this->plugin->isMagicClockEnabled($player)){
            $event->setCancelled(true);
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onPlayerDamageByPlayer(EntityDamageByEntityEvent $event){
        $victim = $event->getEntity();
        $issuer = $event->getDamager();
        if($victim instanceof Player && $issuer instanceof Player){
            if($this->plugin->isMagicClockEnabled($victim) || $this->plugin->isMagicClockEnabled($issuer)){
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
        if($this->plugin->isMagicClockEnabled($player) && $item->getID() == $this->plugin->getConfig()->get("itemID")){
            $this->plugin->toggleMagicClock($player);
            $event->setCancelled(true);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onBlockPlace(BlockPlaceEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        if($this->plugin->isMagicClockEnabled($player) && $item->getID() == $this->plugin->getConfig()->get("itemID")){
            $this->plugin->toggleMagicClock($player);
            $event->setCancelled(true);
        }
    }

    /**
     * @param PluginDisableEvent $event
     *
     * @priority HIGHEST
     */
    public function onPluginDisable(PluginDisableEvent $event){
        $plugin = $event->getPlugin();
        if($plugin->getName() === "EssentialsPE"){
            $this->plugin->essentialspe = false;
        }
    }
}
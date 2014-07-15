<?php
namespace MagicClock;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;

class Loader extends PluginBase implements Listener{
    public function onEnable() {
        @mkdir("plugins/MagicClock");
        $this->checkConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("magicclock", new MagicClockCommand($this));
    }

    public function onDisable(){
        $this->saveDefaultConfig();
    }

    private function checkConfig(){
        if(!is_bool($this->getConfig()->get("enableonjoin"))){
            $this->getConfig()->set(true);
        }
        $this->getConfig()->save();
        return true;
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        if($this->getConfig()->get("enableonjoin") === true){
            $this->players[$player->getName()] = true;
            $this->toggleMagicClock($player);
        }
        if(!$player->hasPermission("magicclock.exempt")){
            foreach($this->getServer()->getOnlinePlayers() as $p){
                if($this->isMagicClockEnabled($p)){
                    $p->hidePlayer($player);
                }
            }
        }
    }

    public function onBlockTouch(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        if($item->getID() == $this->getConfig()->get("itemID")){
            $this->toggleMagicClock($player);
        }
    }

    /*
     *  .----------------.  .----------------.  .----------------.
     * | .--------------. || .--------------. || .--------------. |
     * | |      __      | || |   ______     | || |     _____    | |
     * | |     /  \     | || |  |_   __ \   | || |    |_   _|   | |
     * | |    / /\ \    | || |    | |__) |  | || |      | |     | |
     * | |   / ____ \   | || |    |  ___/   | || |      | |     | |
     * | | _/ /    \ \_ | || |   _| |_      | || |     _| |_    | |
     * | ||____|  |____|| || |  |_____|     | || |    |_____|   | |
     * | |              | || |              | || |              | |
     * | '--------------' || '--------------' || '--------------' |
     *  '----------------'  '----------------'  '----------------'
     *
     */

    protected $players = [];

    public function toggleMagicClock(Player $player){
        if(!$this->isMagicClockEnabled($player)){
            $this->players[$player->getName()] = true;
            foreach($this->getServer()->getOnlinePlayers() as $p){
                if(!$p->hasPermission("magicclock.exempt")){
                    $player->hidePlayer($p);
                }
            }
        }else{
            $this->players[$player->getName()] = false;
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $player->showPlayer($p);
            }
        }
    }

    public function isMagicClockEnabled(Player $player){
        if($this->players[$player->getName()] !== false){
            return true;
        }else{
            return false;
        }
    }
}

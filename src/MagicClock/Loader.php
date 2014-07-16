<?php
namespace MagicClock;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;

class Loader extends PluginBase{
    public function onEnable() {
        @mkdir("plugins/MagicClock");
        $this->checkConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);
        $this->getServer()->getCommandMap()->register("magicclock", new MagicClockCommand($this));
    }

    private function checkConfig(){
        $this->getConfig()->save();
        if(!is_bool($this->getConfig()->get("enableonjoin"))){
            $this->getConfig()->set(true);
        }
        return true;
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

    public $players = [];

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
        if($this->players[$player->getName()] != false){
            return true;
        }else{
            return false;
        }
    }
}

<?php
namespace MagicClock;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{
    public function onEnable() {
        @mkdir("plugins/MagicClock");
        $this->checkConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);
        $this->getServer()->getCommandMap()->register("magicclock", new MagicClockCommand($this));
    }

    private function checkConfig(){
        $this->getConfig()->save();

        if(!$this->getConfig()->exists("enableonjoin")){
            $this->getConfig()->set("enableonjoin", true);
        }elseif(!$this->getConfig()->exists("itemID")){
            $this->getConfig()->set("itemID", 347);
        }elseif(!$this->getConfig()->exists("hideplayersmessage")){
            $this->getConfig()->set("hideplayersmessage", "All players have been hidden");
        }elseif(!$this->getConfig()->exists("showplayersmessage")){
            $this->getConfig()->set("showplayersmessage", "All players have been revelated");
        }elseif(!$this->getConfig()->exists("disablechat")){
            $this->getConfig()->set("disablechat", false);
        }

        if(!is_bool($this->getConfig()->get("enableonjoin"))){
            $this->getConfig()->set(true);
        }elseif(!is_numeric($this->getConfig()->get("itemID"))){
            $this->getLogger()->alert(TextFormat::RED . "Unknown item given in the config.");
            $this->getConfig()->set("itemID", 347);
        }elseif(!is_bool($this->getConfig()->get("disablechat"))){
            $this->getConfig()->set("disablechat", false);
        }

        $this->getConfig()->save();
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
            foreach($player->getLevel()->getPlayers() as $p){
                if(!$p->hasPermission("magicclock.exempt")){
                    $player->hidePlayer($p);
                }
            }
        }else{
            $this->players[$player->getName()] = false;
            foreach($player->getLevel()->getPlayers() as $p){
                $player->showPlayer($p);
            }
        }
    }

    public function isMagicClockEnabled(Player $player){
        if($this->players[$player->getName()] === false){
            return false;
        }else{
            return true;
        }
    }

    public function isChatDisabled(){
        if($this->getConfig()->get("disablechat") === false){
            return false;
        }else{
            return true;
        }
    }
}

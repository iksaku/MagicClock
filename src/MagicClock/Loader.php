<?php
namespace MagicClock;
use MagicClock\OtherEvents\EssentialsPEEvents;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use EssentialsPE\Loader as EssentialsPE;

class Loader extends PluginBase{
    public $essentialspe;

    public function onEnable() {
        @mkdir("plugins/MagicClock");
        $this->checkConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);
        $this->getServer()->getCommandMap()->register("magicclock", new MagicClockCommand($this));

        $ess = $this->getServer()->getPluginManager()->getPlugin("EssentialsPE");
        if($ess instanceof Plugin && $ess->isEnabled()){
            $this->essentialspe = new EssentialsPE();
            $this->getServer()->getPluginManager()->registerEvents(new EssentialsPEEvents($this), $this);
        }
    }

    public function onDisable(){
        foreach($this->getServer()->getOnlinePlayers() as $p){ //Player to show
            foreach($this->getServer()->getOnlinePlayers() as $players){ //Rest of players
                if($this->essentialspe instanceof EssentialsPE && !$this->essentialspe->isVanished($p)){
                    $players->showPlayer($p); //Show $p if isn't vanished by EssentialsPE
                }else{
                    $players->showPlayer($p); //Show $p
                }
            }
        }
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

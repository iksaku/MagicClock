<?php
namespace MagicClock;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;

class Loader extends PluginBase{
    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->checkConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);
        $this->getServer()->getCommandMap()->register("magicclock", new MagicClockCommand($this));
    }

    public function onDisable(){
        foreach($this->getServer()->getOnlinePlayers() as $p){ //Player to show
            foreach($this->getServer()->getOnlinePlayers() as $players){ //Rest of players
                $players->showPlayer($p);
            }
        }
    }

    private function checkConfig(){
        $this->getConfig()->save();
        $cfg = $this->getConfig();
        if(!$cfg->exists("enableonjoin") || !is_bool($cfg->get("enableonjoin"))){
            $cfg->set("enableonjoin", true);
        }
        if(!$cfg->exists("itemID") || !is_numeric($cfg->get("itemID"))){
            $cfg->set("itemID", 347);
        }
        if(!$cfg->exists("hideplayersmessage")){
            $cfg->set("hideplayersmessage", "All players have been hidden");
        }
        if(!$cfg->exists("showplayersmessage")){
            $cfg->set("showplayersmessage", "All players have been revelated");
        }
        if(!$cfg->exists("disablechat") || !is_bool($cfg->get("disablechat"))){
            $cfg->set("disablechat", false);
        }

        $cfg->save();
        $cfg->reload();
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
        return $this->players[$player->getName()];
    }

    public function isChatDisabled(){
        return $this->getConfig()->get("disablechat");
    }
}

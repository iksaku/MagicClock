<?php
namespace MagicClock;
use MagicClock\EventHandlers\EssentialsPEEvents;
use MagicClock\EventHandlers\EventHandler;
use MagicClock\EventHandlers\VanishNPEvents;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use EssentialsPE\Loader as EssentialsPE;
use VanishNP\Loader as VanishNP;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{
    /** @var null|EssentialsPE */
    private $essentialspe;
    /** @var null|VanishNP */
    private $vanishnp;

    public function onEnable(){
        if(!is_dir($this->getDataFolder())){
            mkdir($this->getDataFolder());
        }
        $this->checkConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);

        $this->essentialspe = $this->getServer()->getPluginManager()->getPlugin("EssentialsPE");
        $this->vanishnp = $this->getServer()->getPluginManager()->getPlugin("VanishNP");
        if($this->getEssentialsPEAPI() !== false){
            $this->getServer()->getPluginManager()->registerEvents(new EssentialsPEEvents($this), $this);
            $this->getServer()->getLogger()->info(TextFormat::YELLOW . "Enabled " . TextFormat::GREEN . "EssentialsPE" . TextFormat::YELLOW . " plugin support for " . TextFormat::RED . "MagicClock");
        }elseif($this->getVanishNPAPI() !== false){
            $this->getServer()->getPluginManager()->registerEvents(new VanishNPEvents($this), $this);
            $this->getServer()->getLogger()->info(TextFormat::YELLOW . "Enabled " . TextFormat::GREEN . "EssentialsPE" . TextFormat::YELLOW . " plugin support for " . TextFormat::RED . "MagicClock");
        }

        $this->getServer()->getCommandMap()->register("magicclock", new MagicClockCommand($this));
    }

    public function onDisable(){
        foreach($this->getServer()->getOnlinePlayers() as $p){ //Player to show
            foreach($this->getServer()->getOnlinePlayers() as $player){ //Rest of players
                if(!$this->getEssentialsPEAPI() || (($this->getEssentialsPEAPI() !== false && !$this->getEssentialsPEAPI()->isVanished($player)) || ($this->getVanishNPAPI() !== false && !$this->getVanishNPAPI()->isVanished($player)))){
                    $player->showPlayer($p);
                }
            }
        }
    }

    private function checkConfig(){
        if(!file_exists($this->getDataFolder() . "config.yml")){
            $this->saveDefaultConfig();
        }
        $cfg = $this->getConfig();
        if(!$cfg->exists("version") || $cfg->get("version") !== "1.0.7"){
            $this->getLogger()->debug(TextFormat::RED . "An invalid config file was found, generating a new one...");
            unlink($this->getDataFolder() . "config.yml");
            $this->saveDefaultConfig();
        }

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
    }

    /**
     * @return bool|EssentialsPE
     */
    private function getEssentialsPEAPI(){
        if($this->essentialspe === null){
            return false;
        }
        return $this->essentialspe;
    }

    /**
     * @return bool|VanishNP
     */
    private function getVanishNPAPI(){
        if($this->vanishnp === null){
            return false;
        }
        return $this->vanishnp;
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

    /**
     * @param Player $player
     */
    public function toggleMagicClock(Player $player){
        if(!$this->isMagicClockEnabled($player)){ // Enable MagicClock
            $this->players[] = $player->getName();
            foreach($player->getLevel()->getPlayers() as $p){
                if(!$p->hasPermission("magicclock.exempt")){
                    $player->hidePlayer($p);
                }
            }
        }else{ // Disable MagicClock
            if(in_array($player->getName(), $this->players)){
                $this->players[$player->getName()] = false;
                unset($this->players[$player->getName()]);
            }
            foreach($player->getLevel()->getPlayers() as $p){
                if(!$this->getEssentialsPEAPI() || (($this->getEssentialsPEAPI() !== false && !$this->getEssentialsPEAPI()->isVanished($player)) || ($this->getVanishNPAPI() !== false && !$this->getVanishNPAPI()->isVanished($player)))){
                    $player->showPlayer($p);
                }
            }
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isMagicClockEnabled(Player $player){
        return in_array($player->getName(), $this->players);
    }

    /**
     * @return bool
     */
    public function isChatDisabled(){
        return $this->getConfig()->get("disablechat");
    }
}

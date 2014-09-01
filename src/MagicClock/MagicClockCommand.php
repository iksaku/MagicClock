<?php
namespace MagicClock;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class MagicClockCommand extends Command implements PluginIdentifiableCommand{
    /** @var \MagicClock\Loader  */
    public $plugin;

    public function __construct(Loader $plugin){
        parent::__construct("magicclock", "Toggle the MagicClock", "/magicclock [get]", ["mcclock"]);
        $this->setPermission("magicclock.command");
        $this->plugin = $plugin;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function execute(CommandSender $sender, $alias, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Please run this command in-game");
            return false;
        }
        switch(count($args)){
            case 0:
                $this->plugin->toggleMagicClock($sender);
                if(!$this->plugin->isMagicClockEnabled($sender)){
                    $sender->sendMessage(TextFormat::YELLOW . $this->plugin->getConfig()->get("showplayersmessage"));
                }else{
                    $sender->sendMessage(TextFormat::YELLOW . $this->plugin->getConfig()->get("hideplayersmessage"));
                }
                break;
            case 1:
                switch(strtolower($args[0])){
                    case "get":
                        if(!$sender->hasPermission("magicclock.command.get")){
                            $sender->sendMessage(TextFormat::RED . "You don't have permissions to use this command.");
                            return false;
                        }
                        $sender->sendMessage(TextFormat::YELLOW . "Sending item to your inventory...");
                        $sender->getInventory()->addItem(Item::get($this->plugin->getConfig()->get("itemID")));
                        return true;
                        break;
                    default:
                        $sender->sendMessage(TextFormat::RED . "Usage: /magicclock [get]");
                        break;
                }
                break;
            default:
                $sender->sendMessage(TextFormat::RED . $this->getUsage());
                break;
        }
        return true;
    }
} 
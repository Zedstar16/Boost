<?php

declare(strict_types=1);

namespace Zedstar16\Boost;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;


class Main extends PluginBase implements Listener {

    public $players = [];
    public $boosts = [];
    public $flames;

	public function onEnable() : void{
	    $this->players = [];
	    $this->boosts = [];

	    $this->getServer()->getPluginManager()->registerEvents($this, $this);
	  //  $this->saveResource("config.yml");
	  //  $this->saveDefaultConfig();
       // $period = (int) $this->getConfig()->get("tick-rate");
        $this->getScheduler()->scheduleRepeatingTask(new BoostTask($this), 1);
      //  if($this->getConfig()->get("particles")){
            $this->flames = true;
       // }
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
	    $p = $this->getServer()->getPlayer($sender->getName());
	    $name = $sender->getName();

        $help = "Boost:\n--To Boost--\n- /boost [force] [player]\n--To Stop Boost--\n- /boost [stop] [player]";


		if($command->getName() == "boost") {
            $n = count($args);
            if (!$this->getServer()->isOp($name)) {
                $sender->sendMessage("You do not have permission to use this command");
                return false;
            }
            if (!$sender instanceof Player) {
                $sender->sendMessage("Execute command In Game");
                return false;
            }
            if (isset($args[0]) && $n = 1 && is_numeric($args[0])) {
                $sender->sendMessage("Boost at Level $args[0] started");
                $this->players[$name] = $name;
                $this->boosts[$name] = $args[0];
                /**elseif(isset($args[0]) && $n = 2 && is_numeric($args[0])){
                 * //other player boost
                 * if($this->getServer()->getPlayer($args[1]) !== null){
                 * $target = $this->getServer()->getPlayer($args[1]);
                 * $tn = $target->getName();
                 * $this->players[$tn] = $tn;
                 * $this->boosts[$tn] = $args[0];
                 * $sender->sendMessage("Boost at force $args[0] for $tn started");
                 * $target->sendMessage("Boost at force $args[0] started");
                 * }*/
            } elseif (isset($this->players[$name])) {
                $sender->sendMessage("Boost Disabled");
                unset($this->players[$name]);
                unset($this->boosts[$name]);
            } else $sender->sendMessage("/boost [force]\nTo disable: /boost");
            /**
             * if(isset($args[0]) && ($args[0] == "stop")){
             * if($this->getServer()->getPlayer($args[1]) !== null){
             * $target = $this->getServer()->getPlayer($args[1]);
             * $tn = $target->getName();
             * if(isset($this->players[$tn])) {
             * $sender->sendMessage("Boost Disabled for target");
             * $target = $this->getServer()->getPlayer($args[1]);
             * unset($this->players[$tn]);
             * unset($this->boosts[$tn]);
             * }else $sender->sendMessage("No player with boost found");
             * }elseif(isset($this->players[$args[1]])){
             * $sender->sendMessage("Boost disabled for offline target");
             * unset($this->players[$args[1]]);
             * unset($this->boosts[$args[1]]);
             * }else $sender->sendMessage("No online or offline player with boost found");
             *
             * }
             */

            /**
             * if($sender->hasPermission("boost")) {
             *
             * if (!isset($args[0]) && isset($this->players[$name])) {
             * //unset single player boost
             * unset($this->players[$name]);
             * } else $sender->sendMessage($help);
             *
             * if ($n = 1 && is_numeric($args[0])) {
             * //single player boost
             * $this->players[$name] = $args[0];
             * } elseif ($sender->hasPermission("boost.other")){
             * if(!isset($this->players[$name]) && $this->getServer()->getPlayer($args[0]) !== null && isset($this->players[$this->getServer()->getPlayer($args[0])->getName()])) {
             * //unset other player boost
             * unset($this->players[$this->getServer()->getPlayer($args[0])]);
             * }else $sender->sendMessage($help);
             * } else $sender->sendMessage("You do not have permission to boost other players");
             *
             * if ($n = 2 && $sender->hasPermission("boost.other")) {
             * //other player boost
             * $target = $this->getServer()->getPlayer($args[0]);
             * if ($target !== null) {
             * if (is_numeric($args[1])) {
             * $this->players[$target->getName()] = $args[0];
             * } $sender->sendMessage("Boost value must be a number");
             * } $sender->sendMessage("Target player is not online");
             * } else $sender->sendMessage("You do not have permission to boost other players");
             * }else $sender->sendMessage("You do not have permission to use this command");
             */


        }return true;}

	public function onQuit(PlayerQuitEvent $event){
	    $name = $event->getPlayer()->getName();
	    if(isset($this->players[$name])) unset($this->players[$name]);
        if(isset($this->boosts[$name])) unset($this->boosts[$name]);
	}

	public function onDisable() : void{
	    $this->players = [];
        $this->boosts = [];

	}
}

<?php

declare(strict_types=1);

namespace Zedstar16\Boost;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener
{

    public $players = [];
    public $boosts = [];
    public $flames;

    public function onEnable(): void
    {
        $this->players = [];
        $this->boosts = [];
        $this->getLogger()->info("Boost, by Zedstar16 enabled\nBoost data cleared");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        // I intend to make it configurable soon
        //  $this->saveResource("config.yml");
        //  $this->saveDefaultConfig();
        // $period = (int) $this->getConfig()->get("tick-rate");
        $this->getScheduler()->scheduleRepeatingTask(new BoostTask($this), 1);
        // I intend to make it configurable soon
        //  if($this->getConfig()->get("particles")){
        $this->flames = true;
        // }
    }

    public function setBoosted(String $player, bool $toggle, $boost = 0)
    {
        if ($toggle) {
            $this->players[$player] = $player;
            $this->boosts[$player] = $boost;
        } else {
            unset($this->players[$player]);
            unset($this->boosts[$player]);
        }
    }

    public function isBoosted(String $player): bool
    {
        return isset($this->players[$player]) ? true : false;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        $name = $sender->getName();
        $help = "§d-=-= §bBoost Help §d=-=-\n§e/boost (level)  §aBoost yourself at chosen level\n§e/boost [off]  §aDisable boost for yourself\n§6/boost (player) (level)  §aBoost other players at specified level\n§6/boost (player) off  §aTurn off boost for specified player";
        if ($command->getName() == "boost") {
            if ($sender->hasPermission("boost.self")) {
                if (count($args) == 1) {
                    if ($sender instanceof Player) {
                        if (is_numeric($args[0])) {
                            $this->setBoosted($name, true, (float)$args[0]);
                            $sender->sendMessage(TextFormat::GREEN . "Boost at level $args[0] started");
                        } elseif ($args[0] == "off") {
                            if ($this->isBoosted($name)) {
                                $this->setBoosted($name, false);
                                $sender->sendMessage(TextFormat::GOLD . "Boost disabled");
                            } else $sender->sendMessage(TextFormat::RED . "You aren't currently in boost mode");
                        } else {
                            $sender->sendMessage(TextFormat::RED . "Incorrect usage");
                            $sender->sendMessage($help);
                        }
                    } else $sender->sendMessage(TextFormat::RED . "Only players can boost themself\nUse /boost [player] [level/off] ");
                } elseif (count($args) == 2) {
                    if ($sender->hasPermission("boost")) {
                        $p = $args[0];
                        $boost = $args[1];
                        if ($this->getServer()->getPlayer($p) !== null) {
                            if (is_numeric($boost)) {
                                $pn = $this->getServer()->getPlayer($p)->getName();
                                $sender->sendMessage(TextFormat::GREEN . "Boost at level $boost started for $pn");
                                $this->setBoosted($pn, true, $boost);
                            } elseif ($boost == "off") {
                                $this->setBoosted($this->getServer()->getPlayer($p)->getName(), false);
                                $sender->sendMessage(TextFormat::GOLD . "Boost turned off for $p");
                                $this->getServer()->getPlayer($p)->sendPopup(TextFormat::RED . "Boost disabled");
                            } else $sender->sendMessage(TextFormat::RED . "Boost level must be a number");
                        } else $sender->sendMessage(TextFormat::RED . "Player: $p is not online");
                        $sender->sendMessage(TextFormat::GOLD . "Boost turned off for $p");
                    }
                } else $sender->sendMessage(TextFormat::RED . "You do not have permission to boost other players");

            } elseif (isset($args[0]) && $args[0] == "help") {
                $sender->sendMessage($help);
            } else {
                if ($this->isBoosted($name)) {
                    $this->setBoosted($name, false);
                    $sender->sendMessage(TextFormat::GOLD . "Boost disabled");
                } else $sender->sendMessage($help);
            }
        }

        return true;
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $name = $event->getPlayer()->getName();
        if (isset($this->players[$name])) unset($this->players[$name]);
        if (isset($this->boosts[$name])) unset($this->boosts[$name]);
    }

    public function onDisable(): void
    {
        $this->players = [];
        $this->boosts = [];
    }
}

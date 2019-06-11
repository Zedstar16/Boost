<?php
/**
 * Created by PhpStorm.
 * User: ZZach
 * Date: 23/03/2019
 * Time: 19:31
 */

namespace Zedstar16\Boost;

use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class BoostTask extends Task
{

    private $pl;

    public function __construct(Main $pl)
    {
        $this->pl = $pl;
    }
/**
orange
255 123 0

red
239 35 25

yellow
252 227 0
 */

    public function onRun(Int $currentTick){
        if(empty($this->pl->players)){
            return;
        }

        foreach($this->pl->players as $p){
            if($this->pl->getServer()->getPlayer($p) !== null){
                $boost = $this->pl->boosts[$p];
                $player = $this->pl->getServer()->getPlayer($p);
                $v = $player->getDirectionVector();
                $player->setMotion(new Vector3(($v->getX()*$boost), ($v->getY()*$boost), ($v->getZ()*$boost)));
                $player->sendPopup(TextFormat::AQUA."Boost Enabled");
                $player->getLevel()->addParticle(new DustParticle(new Vector3($player->getX(), $player->getY()+0.4, $player->getZ()), 239, 35, 25));
                $player->getLevel()->addParticle(new DustParticle(new Vector3($player->getX(), $player->getY()+0.2, $player->getZ()), 255, 123, 0));
                $player->getLevel()->addParticle(new DustParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), 252, 227, 0));

            }
        }
    }

}

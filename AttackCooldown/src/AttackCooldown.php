<?php
declare(strict_types=1);

namespace NgLam2911\AttackCooldown;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class AttackCooldown extends PluginBase implements Listener{

	protected int $cooldown = 0;
	protected array $players = [];

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	protected function getPlayerCooldown(Player $player) : int{
		if (!isset($this->players[$player->getUniqueId()->toString()])){
			return 0;
		}
		return $this->players[$player->getUniqueId()->toString()];
	}

	protected function setPlayerCooldown(Player $player, int $value) : void{
		$this->players[$player->getUniqueId()->toString()] = $value;
	}

	public function onAttack(EntityDamageByEntityEvent $event){
		$damager = $event->getDamager();
		if (!$damager instanceof Player){
			return;
		}
		$cooldown = $this->getPlayerCooldown($damager);
		if ($cooldown > time()){
			$event->cancel();
			$timeleft = time() - $cooldown;
			$msg = $this->getConfig()->get("cooldown_message");
			$msg = str_replace("{cooldown}", (string)$timeleft, $msg);
			$damager->sendMessage($msg);
			return;
		}
		$this->setPlayerCooldown($damager, time() + (int)$this->getConfig()->get("cooldown"));
	}


}
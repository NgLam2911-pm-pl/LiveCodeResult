<?php
declare(strict_types=1);

namespace NgLam2911\VeinMiner;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase implements Listener{

	/** @var bool[] */
	protected array $mining = [];

	/** @var string[] */
	protected array $ignored = [];


	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	protected function onDisable() : void{
		//NO
	}

	protected function isMining(Player $player) : bool{
		if (!isset($this->mining[$player->getName()])){
			return false;
		}
		return $this->mining[$player->getName()];
	}

	protected function setMining(Player $player, bool $value) : void{
		$this->mining[$player->getName()] = $value;
	}

	/**
	 * @param BlockBreakEvent $event
	 *
	 * @priority LOW
	 * @handleCancelled FALSE
	 */
	public function onBreak(BlockBreakEvent $event) : void{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$item = $event->getItem();
		if ($this->isMining($player)){
			return;
		}
		$this->setMining($player, true);
		$this->VeinMiner($player, $block, $item);
		$this->setMining($player, false);
	}

	protected function VeinMiner(Player $player, Block $block, Item $item) : void{
		if (in_array($block->getPosition()->__toString(), $this->ignored)){
			return;
		}
		$this->ignored[] = $block->getPosition()->__toString();
		foreach ($block->getAllSides() as $side){ //Mine every block around
			if ($side->isSameType($side)){
				$this->VeinMiner($player, $side, $item);
			}
		}
		$block->getPosition()->getWorld()->useBreakOn($block->getPosition(), $item, $player, true);
	}
}
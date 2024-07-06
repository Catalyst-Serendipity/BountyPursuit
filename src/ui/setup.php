<?php

declare(strict_types=1);

namespace nicholass003\bounty\ui;

use muqsit\invmenu\InvMenu;
use nicholass003\bounty\BountyPursuit;
use nicholass003\bounty\utils\Utils;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use function base64_decode;
use function base64_encode;

class BountySetupGUI{

	private Config $rewards;

	public function __construct(
		private BountyPursuit $plugin
	){
		$this->rewards = new Config($this->plugin->getDataFolder() . "rewards.json", Config::JSON);
	}

	public function send(Player $player) : void{
		$encoded = $this->rewards->get($player->getXuid());
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("§r§8Bounty Rewards Setup");
		$inv = $menu->getInventory();
		if($encoded !== false){
			$inv->setContents(Utils::readContents(base64_decode($encoded, true)));
		}
		$menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) : void{
			if(!empty($inventory->getContents())){
				$this->rewards->set($player->getXuid(), base64_encode(Utils::writeContents($inventory)));
				$this->rewards->save();
			}

			$player->sendMessage("§r§a>> §6Bounty rewards has been updated.");
		});
		$menu->send($player);
	}

	public function getRewards(string $xuid) : array{
		$contents = [];
		$encoded = $this->rewards->get($xuid);
		if($encoded !== false){
			$contents = Utils::readContents($encoded);
		}
		return $contents;
	}
}

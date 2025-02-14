<?php

/*
 * Copyright (c) 2024 - present nicholass003
 *        _      _           _                ___   ___ ____
 *       (_)    | |         | |              / _ \ / _ \___ \
 *  _ __  _  ___| |__   ___ | | __ _ ___ ___| | | | | | |__) |
 * | '_ \| |/ __| '_ \ / _ \| |/ _` / __/ __| | | | | | |__ <
 * | | | | | (__| | | | (_) | | (_| \__ \__ \ |_| | |_| |__) |
 * |_| |_|_|\___|_| |_|\___/|_|\__,_|___/___/\___/ \___/____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  nicholass003
 * @link    https://github.com/nicholass003/
 *
 *
 */

declare(strict_types=1);

namespace nicholass003\bounty\ui;

use nicholass003\bounty\libs\_47370087076c43d9\muqsit\invmenu\InvMenu;
use nicholass003\bounty\BountyPursuit;
use nicholass003\bounty\utils\Utils;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\utils\Config;
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
			$inv->setContents(Utils::readContents($encoded));
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

	public function removeReward(string $xuid) : void{
		if($this->rewards->exists($xuid)){
			$this->rewards->remove($xuid);
			$this->rewards->save();
		}
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
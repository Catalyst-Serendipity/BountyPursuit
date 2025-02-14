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
 * The use of this software is granted only to individuals or organizations who have obtained
 * a valid license from the copyright owner. The license is non-transferable and is limited to
 * personal, non-commercial use.
 *
 * Any form of distribution, reproduction, or use for commercial purposes, whether directly or
 * indirectly, is strictly prohibited without the express written consent of the copyright owner.
 *
 * Modification, decompilation, or reverse engineering of the software is not permitted.
 *
 * By using the software, you agree to abide by the terms of this license.
 *
 * The software is provided "as is," without warranty of any kind, express or implied,
 * including but not limited to the warranties of merchantability, fitness for a particular
 * purpose, and noninfringement. In no event shall the authors or copyright holders be
 * liable for any claim, damages, or other liability, whether in an action of contract,
 * tort, or otherwise, arising from, out of, or in connection with the software or the use
 * or other dealings in the software.
 *
 * For inquiries regarding licensing options, please contact the copyright owner.
 *
 * @author nicholass033
 *
 * Developed by: Catalyst Serendipity
 *
 *
 */

declare(strict_types=1);

namespace nicholass003\bounty\ui;

use muqsit\invmenu\InvMenu;
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

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

namespace nicholass003\bounty\command\subcommand;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use nicholass003\bounty\BountyPursuit;
use nicholass003\bounty\data\BountyDataManager;
use nicholass003\bounty\entity\BountyNPC;
use nicholass003\bounty\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use function array_map;
use function in_array;

class BountyTopSubCommand extends BaseSubCommand{

	/** @var BountyPursuit */
	protected Plugin $plugin;

	protected function prepare() : void{
		$this->setPermission("bountypursuit.command.top");

		$this->registerArgument(0, new RawStringArgument("type"));
		$this->registerArgument(1, new IntegerArgument("rank", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "You must be logged in to use this command.");
			return;
		}

		$manager = $this->plugin->getDataManager();

		if(!isset($args["type"])){
			$sender->sendMessage(TextFormat::RED . "Usage: /{$aliasUsed} <type: string> [rank: int]");
			return;
		}
		$types = [BountyDataManager::DATA_HIGHEST_STREAK, BountyDataManager::DATA_KILL, BountyDataManager::DATA_CURRENT_STREAK];
		if(!in_array($args["type"], $types, true)){
			$sender->sendMessage(TextFormat::RED . "Type not found.");
			$sender->sendMessage(TextFormat::GREEN . "BountyNPC Type List:");
			array_map(function($type) use($sender) : void{
				$sender->sendMessage(TextFormat::YELLOW . "- {$type}\n");
			}, $types);
			return;
		}
		$top = 1;
		if(isset($args["rank"])){
			$top = (int) $args["rank"];
		}
		$skin = Utils::getTopStatsPlayerSkin($manager->getBounties(), $args["type"], $top);
		if($skin === null){
			$skin = $sender->getSkin();
		}
		$npc = new BountyNPC($sender->getLocation(), $skin, Utils::createCustomId(), $top, $args["type"]);
		$npc->spawnToAll();
	}
}

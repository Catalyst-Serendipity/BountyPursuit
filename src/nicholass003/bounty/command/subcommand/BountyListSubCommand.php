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

use nicholass003\bounty\libs\_dd564a5d1203e383\CortexPE\Commando\BaseSubCommand;
use nicholass003\bounty\BountyPursuit;
use nicholass003\bounty\data\BountyDataManager;
use nicholass003\bounty\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use function count;
use function str_replace;
use function time;

class BountyListSubCommand extends BaseSubCommand{

	/** @var BountyPursuit */
	protected Plugin $plugin;

	protected function prepare() : void{
		$this->setPermission("bountypursuit.command.list");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "You must be logged in to use this command.");
			return;
		}

		$manager = $this->plugin->getDataManager();
		$currentTime = time();
		$message = $this->plugin->getConfig()->get("bounty-list", "- from {player}, time: {bounty-time}, target: {target}");
		if(count($manager->getTargets()) === 0){
			$sender->sendMessage(TextFormat::GREEN . "There are no Players on Bounty!");
			return;
		}
		$sender->sendMessage("§r§a>>§6List of Players on Bounty§a<<");
		foreach($manager->getTargets() as $xuid => $data){
			$sender->sendMessage(str_replace(["{player}", "{bounty-time}", "{target}"], [$data[BountyDataManager::TAG_OWNER], Utils::timeFormat($data[BountyDataManager::TAG_TIME] - $currentTime, $this->plugin->getConfig()->get("time-format", "{year}{month}{day}{hour}{minute}{second}")), $data[BountyDataManager::TAG_TARGET]], $message));
		}
	}
}
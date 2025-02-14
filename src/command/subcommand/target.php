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
use nicholass003\bounty\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use function str_replace;
use function time;

class BountyTargetSubCommand extends BaseSubCommand{

	/** @var BountyPursuit */
	protected Plugin $plugin;

	protected function prepare() : void{
		$this->setPermission("bountypursuit.command.target");

		$this->registerArgument(0, new RawStringArgument("target"));
		$this->registerArgument(1, new IntegerArgument("time", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "You must be logged in to use this command.");
			return;
		}

		$manager = $this->plugin->getDataManager();

		if(!isset($args["target"])){
			$sender->sendMessage(TextFormat::RED . "Usage: /{$aliasUsed} <player: string> [time: int]");
			return;
		}

		$target = $this->plugin->getServer()->getPlayerExact($args["target"]);
		if($target === null){
			$sender->sendMessage(TextFormat::RED . "Player not found.");
			return;
		}

		$time = 60; //seconds
		if(isset($args["time"])){
			$time = (int) $args["time"];
		}
		$manager->setTarget($sender, $target, $time);
		$bountyTime = Utils::timeFormat($time, $this->plugin->getConfig()->get("time-format"));
		$this->plugin->getScheduler()->scheduleDelayedTask(new class extends Task{
			public function onRun() : void{
				$manager = BountyPursuit::getInstance()->getDataManager();
				$currentTime = time();
				foreach($manager->getTargets() as $xuid => $data){
					if($currentTime >= $data[BountyDataManager::TAG_TIME]){
						$manager->removeTarget($xuid);
						BountyPursuit::getInstance()->getBountySetup()->removeReward((string) $xuid);
					}
				}
			}
		}, $time * 20);
		$sender->sendMessage(str_replace(["{target}", "{bounty-time}"], [$target->getName(), $bountyTime], $this->plugin->getConfig()->get("bounty-set", "§r§aBounty Set To {target} for {bounty-time}")));
	}
}

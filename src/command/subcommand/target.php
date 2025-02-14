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

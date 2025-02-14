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

use nicholass003\bounty\libs\_1c81b0a033abf4de\CortexPE\Commando\BaseSubCommand;
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
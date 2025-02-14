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

namespace nicholass003\bounty\command;

use nicholass003\bounty\libs\_47370087076c43d9\CortexPE\Commando\BaseCommand;
use nicholass003\bounty\BountyPursuit;
use nicholass003\bounty\command\subcommand\BountyListSubCommand;
use nicholass003\bounty\command\subcommand\BountyRemoveSubCommand;
use nicholass003\bounty\command\subcommand\BountyRewardSubCommand;
use nicholass003\bounty\command\subcommand\BountyTargetSubCommand;
use nicholass003\bounty\command\subcommand\BountyTopSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class BountyCommand extends BaseCommand{

	/** @var BountyPursuit */
	protected Plugin $plugin;

	protected function prepare() : void{
		$this->setPermission("bountypursuit.command");

		$this->registerSubCommand(new BountyListSubCommand($this->plugin, "list", "BountyList Commands"));
		$this->registerSubCommand(new BountyRemoveSubCommand($this->plugin, "remove", "BountyRemove Commands"));
		$this->registerSubCommand(new BountyRewardSubCommand($this->plugin, "reward", "BountyReward Commands"));
		$this->registerSubCommand(new BountyTargetSubCommand($this->plugin, "target", "BountyTarget Commands", ["set"]));
		$this->registerSubCommand(new BountyTopSubCommand($this->plugin, "top", "BountyTop Commands"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$sender->sendMessage(TextFormat::RED . "Usage: /bounty <reward|target|top>");
	}
}
<?php

declare(strict_types=1);

namespace nicholass003\bounty\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use nicholass003\bounty\BountyPursuit;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class BountyRewardSubCommand extends BaseSubCommand{

	/** @var BountyPursuit */
	protected Plugin $plugin;

	protected function prepare() : void{
		$this->setPermission("bountypursuit.command.reward");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "You must be logged in to use this command.");
			return;
		}

		$setup = $this->plugin->getBountySetup();
		$setup->send($sender);
	}
}

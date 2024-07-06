<?php

declare(strict_types=1);

namespace nicholass003\bounty\command\subcommand;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use nicholass003\bounty\BountyPursuit;
use nicholass003\bounty\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use function str_replace;

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
		$sender->sendMessage(str_replace(["{target}", "{bounty-time}"], [$target->getName(), $bountyTime], $this->plugin->getConfig()->get("bounty-set", "§r§aBounty Set To {target} for {bounty-time}")));
	}
}

<?php

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

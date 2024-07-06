<?php

declare(strict_types=1);

namespace nicholass003\bounty\command;

use CortexPE\Commando\BaseCommand;
use nicholass003\bounty\BountyPursuit;
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

        $this->registerSubCommand(new BountyRewardSubCommand($this->plugin, "reward", "BountyReward Commands"));
        $this->registerSubCommand(new BountyTargetSubCommand($this->plugin, "target", "BountyTarget Commands", ["set"]));
        $this->registerSubCommand(new BountyTopSubCommand($this->plugin, "top", "BountyTop Commands"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        $sender->sendMessage(TextFormat::RED . "Usage: /bounty <reward|target|top>");
    }
}
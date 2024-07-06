<?php

declare(strict_types=1);

namespace nicholass003\bounty;

use nicholass003\bounty\data\BountyDataManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener{

	private BountyDataManager $manager;

	public function __construct(
		protected BountyPursuit $plugin
	){
		$this->manager = $this->plugin->getDataManager();
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$this->manager->create($event->getPlayer());
	}
}

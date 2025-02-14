<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\type\graphic\network;

use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\session\InvMenuInfo;
use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;

final class ActorInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator{

	public function __construct(
		readonly private int $actor_runtime_id
	){}

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void{
		$packet->actorUniqueId = $this->actor_runtime_id;
		$packet->blockPosition = new BlockPosition(0, 0, 0);
	}
}
<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_6de1267ac5dfd132\muqsit\invmenu\type\graphic\network;

use nicholass003\bounty\libs\_6de1267ac5dfd132\muqsit\invmenu\session\InvMenuInfo;
use nicholass003\bounty\libs\_6de1267ac5dfd132\muqsit\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

final class WindowTypeInvMenuGraphicNetworkTranslator implements InvMenuGraphicNetworkTranslator{

	public function __construct(
		readonly private int $window_type
	){}

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void{
		$packet->windowType = $this->window_type;
	}
}
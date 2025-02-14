<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\type\graphic\network;

use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\session\InvMenuInfo;
use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}
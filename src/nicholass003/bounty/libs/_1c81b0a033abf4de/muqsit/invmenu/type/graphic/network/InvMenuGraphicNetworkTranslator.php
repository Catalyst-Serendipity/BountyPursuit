<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_1c81b0a033abf4de\muqsit\invmenu\type\graphic\network;

use nicholass003\bounty\libs\_1c81b0a033abf4de\muqsit\invmenu\session\InvMenuInfo;
use nicholass003\bounty\libs\_1c81b0a033abf4de\muqsit\invmenu\session\PlayerSession;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}
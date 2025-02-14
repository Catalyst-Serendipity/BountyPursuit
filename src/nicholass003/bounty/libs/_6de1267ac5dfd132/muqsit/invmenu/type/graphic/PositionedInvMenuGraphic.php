<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_6de1267ac5dfd132\muqsit\invmenu\type\graphic;

use pocketmine\math\Vector3;

interface PositionedInvMenuGraphic extends InvMenuGraphic{

	public function getPosition() : Vector3;
}
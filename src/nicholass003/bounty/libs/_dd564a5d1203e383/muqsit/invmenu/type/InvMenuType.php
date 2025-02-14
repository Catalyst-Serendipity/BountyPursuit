<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\type;

use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\InvMenu;
use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\type\graphic\InvMenuGraphic;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType{

	public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

	public function createInventory() : Inventory;
}
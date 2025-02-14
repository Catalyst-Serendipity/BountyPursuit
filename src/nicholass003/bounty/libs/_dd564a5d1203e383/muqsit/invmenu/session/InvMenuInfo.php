<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\session;

use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\InvMenu;
use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}
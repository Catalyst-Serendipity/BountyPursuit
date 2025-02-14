<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_1c81b0a033abf4de\muqsit\invmenu\session;

use nicholass003\bounty\libs\_1c81b0a033abf4de\muqsit\invmenu\InvMenu;
use nicholass003\bounty\libs\_1c81b0a033abf4de\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		readonly public InvMenu $menu,
		readonly public InvMenuGraphic $graphic,
		readonly public ?string $graphic_name
	){}
}
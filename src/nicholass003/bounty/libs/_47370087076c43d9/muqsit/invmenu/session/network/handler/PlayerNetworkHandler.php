<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_47370087076c43d9\muqsit\invmenu\session\network\handler;

use Closure;
use nicholass003\bounty\libs\_47370087076c43d9\muqsit\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}
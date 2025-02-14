<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_6de1267ac5dfd132\muqsit\invmenu\session\network\handler;

use Closure;
use nicholass003\bounty\libs\_6de1267ac5dfd132\muqsit\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}
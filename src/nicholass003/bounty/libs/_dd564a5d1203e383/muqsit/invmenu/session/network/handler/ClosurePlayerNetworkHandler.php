<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\session\network\handler;

use Closure;
use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\session\network\NetworkStackLatencyEntry;

final class ClosurePlayerNetworkHandler implements PlayerNetworkHandler{

	/**
	 * @param Closure(Closure) : NetworkStackLatencyEntry $creator
	 */
	public function __construct(
		readonly private Closure $creator
	){}

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry{
		return ($this->creator)($then);
	}
}
<?php

declare(strict_types=1);

namespace nicholass003\bounty\data;

final class BountyDataAction{

	private function __construct(){}

	public const NONE = 0;
	public const ADDITION = 1;
	public const SUBTRACTION = 2;
	public const RESET = 3;
}

<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\type\util\builder;

trait AnimationDurationInvMenuTypeBuilderTrait{

	private int $animation_duration = 0;

	public function setAnimationDuration(int $animation_duration) : self{
		$this->animation_duration = $animation_duration;
		return $this;
	}

	protected function getAnimationDuration() : int{
		return $this->animation_duration;
	}
}
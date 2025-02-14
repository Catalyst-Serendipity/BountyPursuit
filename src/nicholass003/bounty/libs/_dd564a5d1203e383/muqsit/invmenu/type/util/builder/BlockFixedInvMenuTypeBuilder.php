<?php

declare(strict_types=1);

namespace nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\type\util\builder;

use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\type\BlockFixedInvMenuType;
use nicholass003\bounty\libs\_dd564a5d1203e383\muqsit\invmenu\type\graphic\network\BlockInvMenuGraphicNetworkTranslator;

final class BlockFixedInvMenuTypeBuilder implements InvMenuTypeBuilder{
	use BlockInvMenuTypeBuilderTrait;
	use FixedInvMenuTypeBuilderTrait;
	use GraphicNetworkTranslatableInvMenuTypeBuilderTrait;

	public function __construct(){
		$this->addGraphicNetworkTranslator(BlockInvMenuGraphicNetworkTranslator::instance());
	}

	public function build() : BlockFixedInvMenuType{
		return new BlockFixedInvMenuType($this->getBlock(), $this->getSize(), $this->getGraphicNetworkTranslator());
	}
}
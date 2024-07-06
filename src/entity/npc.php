<?php

declare(strict_types=1);

namespace nicholass003\bounty\entity;

use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;

class BountyNPC extends Human{

	public const TAG_TOP = "Top";

	protected int $top;

	public function __construct(
		Location $location,
		Skin $skin,
		int $top,
		?CompoundTag $nbt = null
	){
		parent::__construct($location, $skin, $nbt);
		$this->top = $top;
	}

	protected function initEntity(CompoundTag $nbt) : void{
		$tag = $nbt->getTag(self::TAG_TOP);
		if($tag instanceof IntTag){
			$this->top = $tag->getValue();
		}
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		$nbt->setInt(self::TAG_TOP, $this->top);
		return $nbt;
	}
}

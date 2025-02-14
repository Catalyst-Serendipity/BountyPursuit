<?php

/*
 * Copyright (c) 2024 - present nicholass003
 *        _      _           _                ___   ___ ____
 *       (_)    | |         | |              / _ \ / _ \___ \
 *  _ __  _  ___| |__   ___ | | __ _ ___ ___| | | | | | |__) |
 * | '_ \| |/ __| '_ \ / _ \| |/ _` / __/ __| | | | | | |__ <
 * | | | | | (__| | | | (_) | | (_| \__ \__ \ |_| | |_| |__) |
 * |_| |_|_|\___|_| |_|\___/|_|\__,_|___/___/\___/ \___/____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  nicholass003
 * @link    https://github.com/nicholass003/
 *
 *
 */

declare(strict_types=1);

namespace nicholass003\bounty\entity;

use nicholass003\bounty\BountyPursuit;
use nicholass003\bounty\data\BountyDataManager;
use nicholass003\bounty\utils\Utils;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\scheduler\Task;
use pocketmine\world\particle\CriticalParticle;
use function array_key_exists;
use function mt_rand;
use function str_replace;

class BountyNPC extends Human{

	public const TAG_CUSTOM_ID = "CustomID";
	public const TAG_TOP = "Top";
	public const TAG_TYPE = "Type";

	public const TAG_SKIN_DATA = "SkinDataNBT";

	protected string $customId;
	protected int $top;
	protected string $type;

	private int $tick = 0;

	public function __construct(
		Location $location,
		Skin $skin,
		string $customId,
		int $top,
		string $type,
		?CompoundTag $nbt = null
	){
		parent::__construct($location, $skin, $nbt);
		$this->customId = $customId;
		$this->top = $top;
		$this->type = $type;
		$this->setupTopEffects($top);
		$this->update();
		$this->setCanSaveWithChunk(false);
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$tagId = $nbt->getTag(self::TAG_TYPE);
		if($tagId instanceof StringTag){
			$this->type = $tagId->getValue();
		}
		$tagTop = $nbt->getTag(self::TAG_TOP);
		if($tagTop instanceof IntTag){
			$this->top = $tagTop->getValue();
		}
		$tagType = $nbt->getTag(self::TAG_TYPE);
		if($tagType instanceof StringTag){
			$this->type = $tagType->getValue();
		}
		$this->setNameTagAlwaysVisible();
		$this->setHasGravity(false);
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		$nbt->setString(self::TAG_CUSTOM_ID, $this->customId);
		$nbt->setInt(self::TAG_TOP, $this->top);
		$nbt->setString(self::TAG_TYPE, $this->type);
		return $nbt;
	}

	protected function entityBaseTick(int $tickDiff = 1) : bool{
		++$this->tick;
		if($this->tick >= 20 * 10){
			$this->update();
			$this->tick = 0;
		}
		return parent::entityBaseTick($tickDiff);
	}

	private function update() : void {
		$data = Utils::getTopPlayerData(BountyPursuit::getInstance()->getDataManager()->getBounties(), $this->type, $this->top);
		$skin = Utils::getTopStatsPlayerSkin(BountyPursuit::getInstance()->getDataManager()->getBounties(), $this->type, $this->top);
		if($skin !== null){
			$this->setSkin($skin);
		}
		$this->setNameTag(str_replace(["{player}", "{rank-" . $this->type . "}", "{" . $this->type . "-total}"], [$data[BountyDataManager::DATA_NAME], $data["rank"], $data[$this->type]], BountyPursuit::getInstance()->getConfig()->get("bounty-top-" . $this->type)));
	}

	private function setupTopEffects(int $top) : void {
		if($top === 1){
			BountyPursuit::getInstance()->getScheduler()->scheduleRepeatingTask(new class($this) extends Task{
				public function __construct(private BountyNPC $entity){}

				public function onRun() : void {
					if(!$this->entity->isAlive()){
						return;
					}

					$world = $this->entity->getWorld();
					$position = $this->entity->getPosition();
					for($i = 0; $i < 10; $i++) {
						$x = $position->getX() + mt_rand(-100, 100) / 100;
						$y = $position->getY() + mt_rand(0, 200) / 100;
						$z = $position->getZ() + mt_rand(-100, 100) / 100;
						$world->addParticle(new Vector3($x, $y, $z), new CriticalParticle());
					}
				}
			}, 60);
		}
	}

	public function saveData() : void{
		$config = BountyPursuit::getInstance()->getDataNPC();
		$data = [
			"customId" => $this->customId,
			"top" => $this->top,
			"type" => $this->type,
			"skin" => Utils::writeSkinData($this->skin),
			"world" => $this->getWorld()->getFolderName(),
			"pos" => [
				"x" => $this->getPosition()->getFloorX(),
				"y" => $this->getPosition()->getFloorY(),
				"z" => $this->getPosition()->getFloorZ(),
			]
		];
		if(!array_key_exists($this->customId, $data)){
			$config->set($this->customId, $data);
			$config->save();
		}
	}
}
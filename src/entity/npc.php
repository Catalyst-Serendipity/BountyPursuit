<?php

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
use function base64_encode;
use function mt_rand;
use function str_replace;

class BountyNPC extends Human{

	public const TAG_TOP = "Top";
	public const TAG_TYPE = "Type";

	protected int $top;
	protected string $type;

	private int $tick = 0;

	public function __construct(
		Location $location,
		Skin $skin,
		int $top,
		string $type,
		?CompoundTag $nbt = null
	){
		parent::__construct($location, $skin, $nbt);
		$this->top = $top;
		$this->type = $type;
		$this->setupTopEffects($top);
		$this->updateNameTag();
		$this->setCanSaveWithChunk(false);
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
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
		$nbt->setInt(self::TAG_TOP, $this->top);
		$nbt->setString(self::TAG_TYPE, $this->type);
		return $nbt;
	}

	protected function entityBaseTick(int $tickDiff = 1) : bool{
		++$this->tick;
		if($this->tick >= 20 * 10){
			$this->updateNameTag();
			$this->tick = 0;
		}
		return parent::entityBaseTick($tickDiff);
	}

	private function updateNameTag() : void {
		$data = Utils::getTopPlayerData(BountyPursuit::getInstance()->getDataManager()->getBounties(), $this->type, $this->top);
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
		$data = $config->getAll();
		$data[] = [
			"top" => $this->top,
			"type" => $this->type,
			"skin" => [
				"skinId" => base64_encode($this->getSkin()->getSkinId()),
				"skinData" => base64_encode($this->getSkin()->getSkinData()),
				"capeData" => base64_encode($this->getSkin()->getCapeData()),
				"geometryName" => base64_encode($this->getSkin()->getGeometryName()),
				"geometryData" => base64_encode($this->getSkin()->getGeometryData()),
			],
			"world" => $this->getWorld()->getFolderName(),
			"pos" => [
				"x" => $this->getPosition()->getFloorX(),
				"y" => $this->getPosition()->getFloorY(),
				"z" => $this->getPosition()->getFloorZ(),
			]
		];
		$config->setAll($data);
		$config->save();
	}
}

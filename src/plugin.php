<?php

declare(strict_types=1);

namespace nicholass003\bounty;

use muqsit\invmenu\InvMenuHandler;
use nicholass003\bounty\command\BountyCommand;
use nicholass003\bounty\data\BountyDataManager;
use nicholass003\bounty\entity\BountyNPC;
use nicholass003\bounty\ui\BountySetupGUI;
use nicholass003\bounty\utils\Utils;
use pocketmine\command\SimpleCommandMap;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class BountyPursuit extends PluginBase{
	use SingletonTrait;

	private BountySetupGUI $bountySetup;
	private BountyDataManager $dataManager;
	private Config $dataNPC;

	public array $removeSessions = [];

	protected function onEnable() : void{
		self::setInstance($this);

		$this->dataNPC = new Config($this->getDataFolder() . "npcs.yml", Config::YAML);

		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}

		$this->bountySetup = new BountySetupGUI($this);

		$this->dataManager = new BountyDataManager($this);
		$this->dataManager->loadData();

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

		$this->registerCommands($this->getServer()->getCommandMap());
		$this->registerEntities();

		Utils::loadNPCs($this->dataNPC);
	}

	protected function onDisable() : void{
		foreach($this->getServer()->getWorldManager()->getWorlds() as $world){
			foreach($world->getEntities() as $entity){
				if($entity instanceof BountyNPC){
					$entity->saveData();
				}
			}
		}
		$this->dataManager->saveData();
	}

	private function registerCommands(SimpleCommandMap $commandMap) : void{
		$commandMap->register("bountypursuit", new BountyCommand($this, "bounty", "Bounty Command"));
	}

	private function registerEntities() : void{
		$entityFactory = EntityFactory::getInstance();
		$entityFactory->register(BountyNPC::class, function(World $world, CompoundTag $nbt) : BountyNPC{
			$getTagValue = function(CompoundTag $nbt, string $tagName, string $tagClass) : mixed{
				$tag = $nbt->getTag($tagName);
				if($tag instanceof $tagClass){
					return $tag->getValue();
				}else{
					throw new SavedDataLoadingException("Expected \"{$tagName}\" NBT tag of type {$tagClass} not found");
				}
			};
			$customId = $getTagValue($nbt, BountyNPC::TAG_CUSTOM_ID, StringTag::class);
			$top = $getTagValue($nbt, BountyNPC::TAG_TOP, IntTag::class);
			$type = $getTagValue($nbt, BountyNPC::TAG_TYPE, StringTag::class);
			return new BountyNPC(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $customId, $top, $type, $nbt);
		}, ["BountyNPC"]);
	}

	public function getDataManager() : BountyDataManager{
		return $this->dataManager;
	}

	public function getBountySetup() : BountySetupGUI{
		return $this->bountySetup;
	}

	public function getDataNPC() : Config{
		return $this->dataNPC;
	}
}

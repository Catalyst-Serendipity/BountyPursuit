<?php

declare(strict_types=1);

namespace nicholass003\bounty;

use muqsit\invmenu\InvMenuHandler;
use nicholass003\bounty\command\BountyCommand;
use nicholass003\bounty\data\BountyDataManager;
use nicholass003\bounty\entity\BountyNPC;
use nicholass003\bounty\ui\BountySetupGUI;
use pocketmine\command\SimpleCommandMap;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class BountyPursuit extends PluginBase{
	use SingletonTrait;

    private BountySetupGUI $bountySetup;
	private BountyDataManager $dataManager;

	protected function onEnable() : void{
		self::setInstance($this);

        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }

        $this->bountySetup = new BountySetupGUI($this);

		$this->dataManager = new BountyDataManager($this);
		$this->dataManager->loadData();

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->registerCommands($this->getServer()->getCommandMap());
		$this->registerEntities();
	}

    private function registerCommands(SimpleCommandMap $commandMap) : void{
        $commandMap->register("bountypursuit", new BountyCommand($this, "bounty", "Bounty Command"));
    }

	private function registerEntities() : void{
		$entityFactory = EntityFactory::getInstance();
		$entityFactory->register(BountyNPC::class, function(World $world, CompoundTag $nbt) : BountyNPC{
			$topTag = $nbt->getTag(BountyNPC::TAG_TOP);
			if($topTag instanceof IntTag){
				$top = $topTag->getValue();
			}else{
				throw new SavedDataLoadingException("Expected \"" . BountyNPC::TAG_TOP . "\" NBT tag of type " . IntTag::class . "  not found");
			}
			return new BountyNPC(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $top, $nbt);
		}, ["BountyNPC"]);
	}

	public function getDataManager() : BountyDataManager{
		return $this->dataManager;
	}

    public function getBountySetup() : BountySetupGUI{
        return $this->bountySetup;
    }
}

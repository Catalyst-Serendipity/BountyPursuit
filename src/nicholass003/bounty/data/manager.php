<?php

declare(strict_types=1);

namespace nicholass003\bounty\data;

use nicholass003\bounty\BountyPursuit;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use function in_array;
use function time;

class BountyDataManager{

	public const DATA_DEATH = "death";
	public const DATA_KILL = "kill";
	public const DATA_CURRENT_STREAK = "streak";
	public const DATA_HIGHEST_STREAK = "highest-streak";

	public const TAG_OWNER = "owner";
	public const TAG_TARGET = "target";
	public const TAG_TIME = "time";

	private Config $bounties;
	private Config $target;

	private array $data = [];

	private array $targets = [];

	public function __construct(
		protected BountyPursuit $plugin
	){
		$this->bounties = new Config($this->plugin->getDataFolder() . "data.json", Config::JSON);
		$this->target = new Config($this->plugin->getDataFolder() . "target.yml", Config::JSON);
	}

	public function loadData() : void{
		foreach($this->bounties->getAll() as $xuid => $data){
			$this->data[(int) $xuid] = $data;
		}
		foreach($this->target->getAll() as $xuid => $data){
			$this->targets[(int) $xuid] = $data;
		}
	}

	public function create(Player $player) : bool{
		$xuid = (int) $player->getXuid();
		if(!isset($this->data[$xuid])){
			$this->data[$xuid] = [
				self::DATA_DEATH => false,
				self::DATA_CURRENT_STREAK => 0,
				self::DATA_HIGHEST_STREAK => 0,
				self::DATA_KILL => 0
			];
			return true;
		}
		return false;
	}

	public function update(Player $player, array $data, int $action) : void{
		$xuid = (int) $player->getXuid();
		if(isset($this->data[$xuid])){
			foreach($data as $key => $value){
				if(!in_array($key, [self::DATA_CURRENT_STREAK, self::DATA_DEATH, self::DATA_HIGHEST_STREAK, self::DATA_KILL], true)){
					throw new \InvalidArgumentException("Unknown data type: " . $key);
				}
				$this->action($xuid, $key, $value, $action);
			}
		}
	}

	private function action(int $xuid, string $key, mixed $value, int $action) : void{
		switch($action){
			case BountyDataAction::NONE:
				$this->data[$xuid][$key] = $value;
				break;
			case BountyDataAction::ADDITION:
				$this->data[$xuid][$key] += $value;
				break;
			case BountyDataAction::SUBTRACTION:
				$this->data[$xuid][$key] = -$value;
				break;
			case BountyDataAction::RESET:
				$this->data[$xuid][$key] = 0;
				break;
		}
	}

	public function getBounties() : array{
		return $this->data;
	}

	public function getTargets() : array{
		return $this->targets;
	}

	public function getTargetFrom(Player $player) : array{
		return $this->targets[(int) $player->getXuid()] ?? [];
	}

	public function setTarget(Player $player, Player $target, int $seconds) : void{
		$this->targets[(int) $player->getXuid()][self::TAG_OWNER] = $player->getName();
		$this->targets[(int) $player->getXuid()][self::TAG_TARGET] = $target->getName();
		$this->targets[(int) $player->getXuid()][self::TAG_TIME] = time() + $seconds;
	}

	public function saveData() : void{
		foreach($this->data as $xuid => $data){
			$this->bounties->set((string) $xuid, $data);
		}
		foreach($this->targets as $xuid => $data){
			$this->target->set((string) $xuid, $data);
		}
		$this->bounties->save();
		$this->target->save();
	}
}

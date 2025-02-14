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
 * The use of this software is granted only to individuals or organizations who have obtained
 * a valid license from the copyright owner. The license is non-transferable and is limited to
 * personal, non-commercial use.
 *
 * Any form of distribution, reproduction, or use for commercial purposes, whether directly or
 * indirectly, is strictly prohibited without the express written consent of the copyright owner.
 *
 * Modification, decompilation, or reverse engineering of the software is not permitted.
 *
 * By using the software, you agree to abide by the terms of this license.
 *
 * The software is provided "as is," without warranty of any kind, express or implied,
 * including but not limited to the warranties of merchantability, fitness for a particular
 * purpose, and noninfringement. In no event shall the authors or copyright holders be
 * liable for any claim, damages, or other liability, whether in an action of contract,
 * tort, or otherwise, arising from, out of, or in connection with the software or the use
 * or other dealings in the software.
 *
 * For inquiries regarding licensing options, please contact the copyright owner.
 *
 * @author nicholass033
 *
 * Developed by: Catalyst Serendipity
 *
 *
 */

declare(strict_types=1);

namespace nicholass003\bounty\data;

use nicholass003\bounty\BountyPursuit;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use function in_array;
use function time;

class BountyDataManager{

	public const DATA_KILL = "kill";
	public const DATA_CURRENT_STREAK = "streak";
	public const DATA_HIGHEST_STREAK = "highest-streak";
	public const DATA_NAME = "name";

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
				self::DATA_NAME => $player->getName(),
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
				if(!in_array($key, [self::DATA_CURRENT_STREAK, self::DATA_HIGHEST_STREAK, self::DATA_KILL], true)){
					throw new \InvalidArgumentException("Unknown data type: " . $key);
				}
				$this->action($xuid, $key, $value, $action);
			}
		}
	}

	public function get(Player $player, string $key) : mixed{
		return $this->data[(int) $player->getXuid()][$key] ?? [];
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

	public function getTargetTo(Player $victim) : array{
		$result = [];
		foreach($this->targets as $xuid => $data){
			if($data[self::TAG_TARGET] === $victim->getName()){
				$result = $data;
				$result["xuid"] = $xuid;
				break;
			}
		}
		return $result;
	}

	public function setTarget(Player $player, Player $target, int $seconds) : void{
		$this->targets[(int) $player->getXuid()][self::TAG_OWNER] = $player->getName();
		$this->targets[(int) $player->getXuid()][self::TAG_TARGET] = $target->getName();
		$this->targets[(int) $player->getXuid()][self::TAG_TIME] = time() + $seconds;
	}

	public function removeTarget(int $xuid) : void{
		if(isset($this->targets[$xuid])){
			unset($this->targets[$xuid]);
		}
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

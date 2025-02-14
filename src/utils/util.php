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

namespace nicholass003\bounty\utils;

use nicholass003\bounty\entity\BountyNPC;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\Server;
use pocketmine\utils\Config;
use function abs;
use function array_keys;
use function array_values;
use function base64_decode;
use function base64_encode;
use function bin2hex;
use function floor;
use function number_format;
use function random_bytes;
use function str_replace;
use function trim;
use function uasort;
use function zlib_decode;
use function zlib_encode;
use const ZLIB_ENCODING_GZIP;

class Utils{

	public const TAG_INVENTORY = "Inventory";

	public static function getSortedArrayBoard(array $data, string $type) : array{
		uasort($data, function($a, $b) use($type) {
			return $b[$type] <=> $a[$type];
		});
		return $data;
	}

	public static function getTopPlayerData(array $data, string $type, int $top) : array{
		$result = [];
		$num = 1;
		foreach(self::getSortedArrayBoard($data, $type) as $xuid => $userData){
			if($num === $top){
				$result = $userData;
				$result["rank"] = $top;
				break;
			}
			++$num;
		}
		return $result;
	}

	public static function getTopStatsPlayerSkin(array $data, string $type, int $top) : ?Skin{
		$playerName = "";
		$num = 1;
		foreach(self::getSortedArrayBoard($data, $type) as $xuid => $userData){
			if($num === $top){
				$playerName = $userData["name"];
				break;
			}
			++$num;
		}

		$player = Server::getInstance()->getPlayerByPrefix($playerName);
		if($player !== null){
			return Human::parseSkinNBT($player->getSaveData());
		}else{
			$playerData = Server::getInstance()->getOfflinePlayerData($playerName);
			return $playerData !== null ? Human::parseSkinNBT($playerData) : null;
		}
	}
	public static function formatNumber(int $number, int $precision = 3) : string {
		$divisors = [
			1000 ** 0 => "", // One
			1000 ** 1 => "K", // Thousand
			1000 ** 2 => "M", // Million
			1000 ** 3 => "B", // Billion
			1000 ** 4 => "T", // Trillion
		];

		foreach($divisors as $divisor => $shorthand){
			if(abs($number) < ($divisor * 1000)){
				break;
			}
		}
		return (float) number_format($number / $divisor, $precision) . $shorthand;
	}

	public static function readContents(string $data) : array{
		$contents = [];
		$invTag = (new BigEndianNbtSerializer())->read(zlib_decode(base64_decode($data, true)))->mustGetCompoundTag()->getListTag(self::TAG_INVENTORY);
		/** @var CompoundTag $tag */
		foreach($invTag as $tag){
			$contents[$tag->getByte("Slot")] = Item::nbtDeserialize($tag);
		}
		return $contents;
	}

	public static function writeContents(Inventory $inventory) : string{
		$contents = [];
		foreach($inventory->getContents() as $slot => $item){
			$contents[] = $item->nbtSerialize($slot);
		}

		return zlib_encode((new BigEndianNbtSerializer())->write(new TreeRoot(CompoundTag::create()
				->setTag(self::TAG_INVENTORY, new ListTag($contents, NBT::TAG_Compound)))), ZLIB_ENCODING_GZIP);
	}

	public static function timeFormat(int $time, string $format) : string{
		$years = floor($time / (365 * 24 * 60 * 60));
		$months = floor(($time - ($years * 365 * 24 * 60 * 60)) / (30 * 24 * 60 * 60));
		$days = floor(($time - ($years * 365 * 24 * 60 * 60) - ($months * 30 * 24 * 60 * 60)) / (24 * 60 * 60));
		$hours = floor(($time - ($years * 365 * 24 * 60 * 60) - ($months * 30 * 24 * 60 * 60) - ($days * 24 * 60 * 60)) / (60 * 60));
		$minutes = floor(($time - ($years * 365 * 24 * 60 * 60) - ($months * 30 * 24 * 60 * 60) - ($days * 24 * 60 * 60) - ($hours * 60 * 60)) / 60);
		$seconds = $time % 60;

		$replacements = [
			'{year}' => $years > 0 ? $years . 'y ' : '',
			'{month}' => $months > 0 ? $months . 'm ' : '',
			'{day}' => $days > 0 ? $days . 'd ' : '',
			'{hour}' => $hours > 0 ? $hours . 'h ' : '',
			'{minute}' => $minutes > 0 ? $minutes . 'm ' : '',
			'{second}' => $seconds > 0 ? $seconds . 's' : ''
		];
		return trim(str_replace(array_keys($replacements), array_values($replacements), $format));
	}

	public static function loadNPCs(Config $dataNPC) : void{
		foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
			foreach($dataNPC->getAll() as $index => $data){
				if($world->getFolderName() === $data["world"]){
					$pos = $data["pos"];
					$skin = $data["skin"];
					$npc = new BountyNPC(Location::fromObject(new Vector3((int) $pos["x"], (int) $pos["y"], (int) $pos["z"]), $world),
							self::readSkinData($skin),
							$data["customId"],
							$data["top"],
							$data["type"]
					);
					$npc->spawnToAll();
				}
			}
		}
	}

	public static function createCustomId() : string{
		return bin2hex(random_bytes(8));
	}

	public static function writeSkinData(Skin $skin) : string{
		$nbt = CompoundTag::create()
		->setString("SkinId", $skin->getSkinId())
		->setByteArray("SkinData", $skin->getSkinData())
		->setByteArray("CapeData", $skin->getCapeData())
		->setString("GeometryName", $skin->getGeometryName())
		->setByteArray("GeometryData", $skin->getGeometryData());
		return base64_encode(zlib_encode((new BigEndianNbtSerializer())->write(new TreeRoot($nbt, BountyNPC::TAG_SKIN_DATA)), ZLIB_ENCODING_GZIP));
	}

	public static function readSkinData(string $data) : ?Skin{
		$tag = (new BigEndianNbtSerializer())->read(zlib_decode(base64_decode($data, true)))->getTag(BountyNPC::TAG_SKIN_DATA);
		/** @var CompountTag $tag */
		return new Skin($tag->getString("SkinId"), $tag->getByteArray("SkinData"), $tag->getByteArray("CapeData"), $tag->getString("GeometryName"), $tag->getByteArray("GeometryData"));
	}
}

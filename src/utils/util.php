<?php

declare(strict_types=1);

namespace nicholass003\bounty\utils;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\TreeRoot;
use function array_keys;
use function array_values;
use function floor;
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

	public static function readContents(string $data) : array{
		$contents = [];
		$invTag = (new BigEndianNbtSerializer())->read(zlib_decode($data))->mustGetCompoundTag()->getListTag(self::TAG_INVENTORY);
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
}

<?php

declare(strict_types=1);

namespace nicholass003\bounty;

use nicholass003\bounty\data\BountyDataAction;
use nicholass003\bounty\data\BountyDataManager;
use nicholass003\bounty\entity\BountyNPC;
use nicholass003\bounty\ui\BountySetupGUI;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function atan2;
use function count;
use const M_PI;

class EventListener implements Listener{

	private BountyDataManager $manager;
	private BountySetupGUI $bounty;

	public function __construct(
		protected BountyPursuit $plugin
	){
		$this->manager = $this->plugin->getDataManager();
		$this->bounty = $this->plugin->getBountySetup();
	}

	public function onEntityDamage(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		if($entity instanceof BountyNPC){
			$event->cancel();
		}
		if($event instanceof EntityDamageByEntityEvent){
			$victim = $event->getEntity();
			$damager = $event->getDamager();
			if($damager instanceof Player && $victim instanceof BountyNPC){
				if(isset($this->plugin->removeSessions[$damager->getName()])){
					$victim->flagForDespawn();
					$damager->sendMessage(TextFormat::YELLOW . "Success remove BountyNPC!");
					unset($this->plugin->removeSessions[$damager->getName()]);
				}
			}
		}
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$this->manager->create($event->getPlayer());
	}

	public function onPlayerDeath(PlayerDeathEvent $event) : void{
		$victim = $event->getPlayer();
		$cause = $victim->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$this->manager->update($victim, [BountyDataManager::DATA_CURRENT_STREAK => 0], BountyDataAction::RESET);
				$this->manager->update($damager, [BountyDataManager::DATA_CURRENT_STREAK => 1], BountyDataAction::ADDITION);
				$this->manager->update($damager, [BountyDataManager::DATA_KILL => 1], BountyDataAction::ADDITION);
				$bounties = $this->manager->getTargetTo($victim);
				if(count($bounties) > 0){
					$rewards = $this->bounty->getRewards((string) $bounties["xuid"]);
					foreach($rewards as $slot => $item){
						$itemEntity = $damager->getWorld()->dropItem($damager->getPosition(), $item);
						if($itemEntity !== null){
							$itemEntity->setOwner($damager->getName());
						}
					}
					$currentStreak = $this->manager->get($damager, BountyDataManager::DATA_CURRENT_STREAK);
					if($currentStreak > $this->manager->get($damager, BountyDataManager::DATA_HIGHEST_STREAK)){
						$this->manager->update($damager, [BountyDataManager::DATA_HIGHEST_STREAK => $currentStreak], BountyDataAction::NONE);
					}
				}
			}
		}
	}

	public function onEntityPickup(EntityItemPickupEvent $event) : void{
		$itemEntity = $event->getOrigin();
		$player = $event->getEntity();
		if($itemEntity instanceof ItemEntity && $player instanceof Player){
			if($itemEntity->getOwner() !== $player->getName()){
				$event->cancel();
			}
		}
	}

	public function onPlayerMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();
		$from = $event->getFrom();
		$to = $event->getTo();

		if($from->distance($to) < 0.1){
			return;
		}

		$maxDistance = 16;
		foreach($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $player) as $entity){
			if($entity instanceof Player){
				continue;
			}

			$xdiff = $player->getLocation()->x - $entity->getLocation()->x;
			$zdiff = $player->getLocation()->z - $entity->getLocation()->z;
			$angle = atan2($zdiff, $xdiff);
			$yaw = (($angle * 180) / M_PI) - 90;
			$ydiff = $player->getLocation()->y - $entity->getLocation()->y;
			$v = new Vector2($entity->getLocation()->x, $entity->getLocation()->z);
			$dist = $v->distance(new Vector2($player->getLocation()->x, $player->getLocation()->z));
			$angle = atan2($dist, $ydiff);
			$pitch = (($angle * 180) / M_PI) - 90;

			if($entity instanceof BountyNPC){
				$pk = MovePlayerPacket::create($entity->getId(), $entity->getPosition()->add(0, $entity->getEyeHeight(), 0), $pitch, $yaw, $yaw, MovePlayerPacket::MODE_NORMAL, $entity->onGround, 0, 0, 0, 0);
				$player->getNetworkSession()->sendDataPacket($pk);
			}
		}
	}
}

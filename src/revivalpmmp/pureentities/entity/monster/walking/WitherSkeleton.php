<?php

/*  PureEntitiesX: Mob AI Plugin for PMMP
    Copyright (C) 2017 RevivalPMMP

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>. */

namespace revivalpmmp\pureentities\entity\monster\walking;

use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use revivalpmmp\pureentities\entity\monster\WalkingMonster;
use pocketmine\entity\Entity;
use pocketmine\item\StoneSword;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use revivalpmmp\pureentities\data\Data;

class WitherSkeleton extends WalkingMonster {
    const NETWORK_ID = Data::WITHER_SKELETON;

    public $height = 2;
    public $width = 0.781;
    public $length = 0.875;
    public $speed = 1.0;

    public function getSpeed(): float {
        return $this->speed;
    }

    public function getName(): string {
        return "Wither Skeleton";
    }

    public function initEntity() {
        parent::initEntity();

        $this->setDamage([0, 3, 4, 6]);
    }

    public function setHealth($amount) {
        parent::setHealth($amount);

        if ($this->isAlive()) {
            if (15 < $this->getHealth()) {
                $this->setDamage([0, 2, 3, 4]);
            } else if (10 < $this->getHealth()) {
                $this->setDamage([0, 3, 4, 6]);
            } else if (5 < $this->getHealth()) {
                $this->setDamage([0, 3, 5, 7]);
            } else {
                $this->setDamage([0, 4, 6, 9]);
            }
        }
    }

    public function spawnTo(Player $player) {
        parent::spawnTo($player);

        $pk = new MobEquipmentPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->item = new StoneSword();
        $pk->inventorySlot = 10;
        $pk->hotbarSlot = 10;
        $player->dataPacket($pk);
    }

    public function attackEntity(Entity $player) {
        if ($this->attackDelay > 10 && $this->distanceSquared($player) < 2) {
            $this->attackDelay = 0;

            $ev = new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getDamage());
            $player->attack($ev);

            $this->checkTamedMobsAttack($player);
        }
    }

    public function getDrops(): array {
        $drops = [];
        if ($this->isLootDropAllowed()) {
            array_push($drops, Item::get(Item::COAL, 0, mt_rand(0, 1)));
            array_push($drops, Item::get(Item::BONE, 0, mt_rand(0, 2)));
            switch (mt_rand(0, 8)) {
                case 1:
                    array_push($drops, Item::get(Item::MOB_HEAD, 1, mt_rand(0, 2)));
                    break;
            }
        }
        return $drops;
    }

    public function getMaxHealth() {
        return 20;
    }

    public function getKillExperience(): int {
        return 5;
    }

}

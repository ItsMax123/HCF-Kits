<?php

namespace ItsMax123\HCF;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerInteractEvent;
use DaPigGuy\PiggyFactions\players\PlayerManager;

class Main extends PluginBase implements Listener{

    private $backstabcooldown;
    private $bardcooldown;
    private $config;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
    }

    public function onInteract(ProjectileHitBlockEvent $event){
        $pot = $event->getEntity();
        if(!$pot instanceof SplashPotion) return;
        $player = $pot->getOwningEntity();
        if(!$player) return;
        $distance = $pot->distance($player);
        if($player instanceof Player && $distance <= 2 && $player->isAlive()){
            if($pot->getPotionId() == 22) {
                $player->setHealth($player->getHealth() + 5);
            } elseif($pot->getPotionId() == 21) {
                $player->setHealth($player->getHealth() + 3);
            }
        }
    }

    public function onDamage(EntityDamageEvent $event) {
        if($event instanceof EntityDamageByEntityEvent) {
            $cooldown = $this->config->get("backstab-cooldown");
            $victim = $event->getEntity();
            $victimn = $event->getEntity()->getName();
            $attacker = $event->getDamager();
            $attackern = $event->getDamager()->getName();
            if($victim instanceof Player and $attacker instanceof Player) {
                if($attacker->getInventory()->getItemInHand()->getId() == 283 && $attacker->getArmorInventory()->getBoots()->getId() == 305 && $attacker->getArmorInventory()->getLeggings()->getId() == 304 && $attacker->getArmorInventory()->getChestplate()->getId() == 303 && $attacker->getArmorInventory()->getHelmet()->getId() == 302 && $attacker->getDirection() == $victim->getDirection()) {
                    if (isset($this->backstabcooldown[$attackern]) and time() - $this->backstabcooldown[$attackern] < $cooldown) {
                        $time = time() - $this->backstabcooldown[$attackern];
                        $count = $cooldown - $time;
                        $attacker->sendMessage("You are on Rogue cooldown for " . $count . " seconds.");
                    } else {
                        $victim->setHealth($victim->getHealth() / 2);
                        $attacker->getInventory()->setItemInHand(item::get(0));
                        $attacker->sendMessage("You hit " . $victimn . " who is now at " . $victim->getHealth() . " health.");
                        $this->backstabcooldown[$attackern] = time();
                        if (strtolower($this->config->get("attacker-effects")) == true) {
                            $config = $this->config->get("attacker");
                            foreach($config as $effect){
                                if(!isset($effect["effect"]))continue;
                                $effectID = Effect::getEffect((int) $effect["effect"]);
                                $duration = isset($effect["duration"]) ? (int) $effect["duration"] : 10;
                                $amplifier = isset($effect["amplifier"]) ? (int) $effect["amplifier"] : 1;
                                $attacker->addEffect(new EffectInstance($effectID, $duration * 20, $amplifier - 1));
                            }
                        }
                        if (strtolower($this->config->get("victim-effects")) == true) {
                            $config = $this->config->get("victim");
                            foreach($config as $effect){
                                if(!isset($effect["effect"]))continue;
                                $effectID = Effect::getEffect((int) $effect["effect"]);
                                $duration = isset($effect["duration"]) ? (int) $effect["duration"] : 10;
                                $amplifier = isset($effect["amplifier"]) ? (int) $effect["amplifier"] : 1;
                                $victim->addEffect(new EffectInstance($effectID, $duration * 20, $amplifier - 1));
                            }
                        }
                    }
                }
            }
        }
    }

    public function onMove(PlayerMoveEvent $event) {
        $entity = $event->getPlayer();
        $boots = $entity->getArmorInventory()->getBoots()->getId();
        $legs = $entity->getArmorInventory()->getLeggings()->getId();
        $chest = $entity->getArmorInventory()->getChestplate()->getId();
        $helmet = $entity->getArmorInventory()->getHelmet()->getId();
        if ($boots == 305 && $legs == 304 && $chest == 303 && $helmet == 302) {
            $rogue = $this->config->get("rogue-armor");
            foreach($rogue as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = Effect::getEffect((int) $effect["effect"]);
                $amplifier = isset($effect["amplifier"]) ? (int) $effect["amplifier"] : 0;
                $visible = isset($effect["visible"]) ? (bool) $effect["visible"] : false;
                $entity->addEffect(new EffectInstance($effectID, 119999, $amplifier - 1, $visible));
            }
        }
        if ($boots == 313 && $legs == 312 && $chest == 311 && $helmet == 310) {
            $diamond = $this->config->get("diamond-armor");
            foreach($diamond as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = Effect::getEffect((int) $effect["effect"]);
                $amplifier = isset($effect["amplifier"]) ? (int) $effect["amplifier"] : 0;
                $visible = isset($effect["visible"]) ? (bool) $effect["visible"] : false;
                $entity->addEffect(new EffectInstance($effectID, 119999, $amplifier - 1, $visible));
            }
        }
        if ($boots == 309 && $legs == 308 && $chest == 307 && $helmet == 306) {
            $minor = $this->config->get("minor-armor");
            foreach($minor as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = Effect::getEffect((int) $effect["effect"]);
                $amplifier = isset($effect["amplifier"]) ? (int) $effect["amplifier"] : 0;
                $visible = isset($effect["visible"]) ? (bool) $effect["visible"] : false;
                $entity->addEffect(new EffectInstance($effectID, 119999, $amplifier - 1, $visible));
            }
        }
        if ($boots == 301 && $legs == 300 && $chest == 299 && $helmet == 298) {
            $archer = $this->config->get("archer-armor");
            foreach($archer as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = Effect::getEffect((int) $effect["effect"]);
                $amplifier = isset($effect["amplifier"]) ? (int) $effect["amplifier"] : 0;
                $visible = isset($effect["visible"]) ? (bool) $effect["visible"] : false;
                $entity->addEffect(new EffectInstance($effectID, 119999, $amplifier - 1, $visible));
            }
        }
        if ($boots == 317 && $legs == 316 && $chest == 315 && $helmet == 314) {
            $bard = $this->config->get("bard-armor");
            foreach($bard as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = Effect::getEffect((int) $effect["effect"]);
                $amplifier = isset($effect["amplifier"]) ? (int) $effect["amplifier"] : 0;
                $visible = isset($effect["visible"]) ? (bool) $effect["visible"] : false;
                $entity->addEffect(new EffectInstance($effectID, 119999, $amplifier - 1, $visible));
            }
        }
    }

    public function onArmorChange(EntityArmorChangeEvent $event) {
        $entity = $event->getEntity();
        $oldItem = $event->getOldItem()->getId();
        if($oldItem == 305 OR $oldItem == 304 OR $oldItem == 303 OR $oldItem == 302) {
            $rogue = $this->config->get("rogue-armor");
            foreach($rogue as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = $effect["effect"];
                $entity->removeEffect($effectID);
            }
        }
        if($oldItem == 310 OR $oldItem == 311 OR $oldItem == 312 OR $oldItem == 313) {
            $diamond = $this->config->get("diamond-armor");
            foreach($diamond as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = $effect["effect"];
                $entity->removeEffect($effectID);
            }
        }
        if($oldItem == 306 OR $oldItem == 307 OR $oldItem == 308 OR $oldItem == 309) {
            $minor = $this->config->get("minor-armor");
            foreach($minor as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = $effect["effect"];
                $entity->removeEffect($effectID);
            }
        }
        if($oldItem == 301 OR $oldItem == 300 OR $oldItem == 299 OR $oldItem == 298) {
            $archer = $this->config->get("archer-armor");
            foreach($archer as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = $effect["effect"];
                $entity->removeEffect($effectID);
            }
        }
        if($oldItem == 314 OR $oldItem == 315 OR $oldItem == 316 OR $oldItem == 317) {
            $archer = $this->config->get("bard-armor");
            foreach($archer as $effect){
                if(!isset($effect["effect"]))continue;
                $effectID = $effect["effect"];
                $entity->removeEffect($effectID);
            }
        }
    }

    public function onHold(PlayerItemHeldEvent $event)
    {
        $entity = $event->getPlayer();
        if ($entity->getArmorInventory()->getBoots()->getId() == 317 && $entity->getArmorInventory()->getLeggings()->getId() == 316 && $entity->getArmorInventory()->getChestplate()->getId() == 315 && $entity->getArmorInventory()->getHelmet()->getId() == 314) {
            $barditems = $this->config->get("bard-items");
            foreach($barditems as $effect){
                if(!isset($effect["item"]) and !isset($effect["effect"]))continue;
                $item = $event->getItem()->getId();
                $itemID = (int) $effect["item"];
                if($item === $itemID) {
                    foreach($this->getServer()->getOnlinePlayers() as $onlineplayer){
                        if($entity !== $onlineplayer) {
                            $distance = $entity->distance($onlineplayer);
                            if ($distance <= 30){
                                $effectID = Effect::getEffect((int) $effect["effect"]);
                                $duration = isset($effect["duration"]) ? (int) $effect["duration"] : 10;
                                $amplifier = isset($effect["amplifier"]) ? (int) $effect["amplifier"] : 1;
                                $entity->addEffect(new EffectInstance($effectID, $duration * 20, $amplifier - 1));
                                $PiggyFactions = $this->getServer()->getPluginManager()->getPlugin("PiggyFactions");
                                if (!is_null($PiggyFactions)) {
                                    $faction = PlayerManager::getInstance()->getPlayer($onlineplayer)->getFaction();
                                    $bardfaction = PlayerManager::getInstance()->getPlayer($entity)->getFaction();
                                    if(!is_null($faction) && !is_null($bardfaction)) {
                                        if($faction->getId() === $bardfaction->getId()) {
                                            $onlineplayer->addEffect(new EffectInstance($effectID, $duration * 20, $amplifier - 1));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function onUse(PlayerInteractEvent $event)
    {
        $entity = $event->getPlayer();
        if ($entity->getArmorInventory()->getBoots()->getId() == 317 && $entity->getArmorInventory()->getLeggings()->getId() == 316 && $entity->getArmorInventory()->getChestplate()->getId() == 315 && $entity->getArmorInventory()->getHelmet()->getId() == 314) {
            $action = $event->getAction();
            if ($action == $event::LEFT_CLICK_AIR or $action == $event::RIGHT_CLICK_AIR) {
                $barditems = $this->config->get("bard-items");
                foreach($barditems as $effect){
                    if(!isset($effect["item"]) and !isset($effect["effect"]))continue;
                    $item = $event->getItem();
                    $itemID = (int) $effect["item"];
                    if($item->getId() === $itemID) {
                        foreach($this->getServer()->getOnlinePlayers() as $onlineplayer){
                            if($entity !== $onlineplayer) {
                                $distance = $entity->distance($onlineplayer);
                                if ($distance <= 30){
                                    $cooldown = $this->config->get("bard-cooldown");
                                    $player = $entity->getName();
                                    $effectName = (string) $effect["name"];
                                    $playereffect = $player . ":" . $effectName;
                                    if (isset($this->bardcooldown[$playereffect]) and time() - $this->bardcooldown[$playereffect] < $cooldown) {
                                        $time = time() - $this->bardcooldown[$playereffect];
                                        $count = $cooldown - $time;
                                        $explode = explode(":",$playereffect);
                                        $entity->sendMessage($explode[1] . " is on cooldown for " . $count . " seconds");
                                    } else {
                                        $effectID = Effect::getEffect((int) $effect["effect"]);
                                        $duration = isset($effect["duration"]) ? (int) $effect["duration"] : 10;
                                        $amplifier = isset($effect["amplifier"]) ? (int) $effect["amplifier"] + 1: 1;
                                        $entity->addEffect(new EffectInstance($effectID, $duration * 20, $amplifier - 1));
                                        $item->pop();
                                        $entity->getInventory()->setItemInHand($item);
                                        $this->bardcooldown[$playereffect] = time();
                                        $PiggyFactions = $this->getServer()->getPluginManager()->getPlugin("PiggyFactions");
                                        if (!is_null($PiggyFactions)) {
                                            $faction = PlayerManager::getInstance()->getPlayer($onlineplayer)->getFaction();
                                            $bardfaction = PlayerManager::getInstance()->getPlayer($entity)->getFaction();
                                            if(!is_null($faction) && !is_null($bardfaction)) {
                                                if($faction->getId() === $bardfaction->getId()) {
                                                    $onlineplayer->addEffect(new EffectInstance($effectID, $duration * 20, $amplifier - 1));
                                                    $onlineplayer->sendMessage("You received " . $effectName . " " . (string) $amplifier . " for " . (string) $duration . " seconds from your bard " . $player);
                                                    $entity->sendMessage("You gave " . $onlineplayer->getName() . " "  . $effectName . " " . (string) $amplifier . " for " . (string) $duration . " seconds");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}
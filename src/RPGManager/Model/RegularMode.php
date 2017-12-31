<?php

namespace RPGManager\Model;

use RPGManager\Entity\CharacterInventory;
use RPGManager\Entity\ItemLocation;
use RPGManager\Utils\CharacterUtils;
use RPGManager\Utils\ItemUtils;
use RPGManager\Utils\MonsterUtils;
use RPGManager\Utils\NpcUtils;

class RegularMode extends Game
{
    private static $instance = null;
    protected $basicActions = ['location', 'inventory', 'carac', 'speak', 'move', 'take', 'drop', 'attack'];

    private static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new RegularMode();
        }
        return self::$instance;
    }

    public static function startGame($entityManager, $settings)
    {
        $game = RegularMode::getInstance();
	    $game::$settings = $settings;

	    $game->writeAccessLog(__METHOD__);
        $game->setEntityManager($entityManager);

        while (true) {
            echo "\nAVAILABLE ACTIONS:\n";
            $actions = $game->basicActions;
            foreach ($actions as $value) {
                echo $value . "  ";
            }
            echo "\n >> ";

            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            $args = explode(" ", $line);
            $game->setArgs($args);
            $game->executePlayerAction($args, $actions);
        }
    }

    private function executePlayerAction($args, $availableActions)
    {
        $this->writeAccessLog(__METHOD__);

        $this->currentPlayer = trim($args[0]);
        $characterUtils = new CharacterUtils();

        if ($characterUtils->isPlayerExist($this->currentPlayer, $this->em)) {
            foreach ($availableActions as $value) {
                if ($this->isValidAction($value, $args)) {
                    if (isset($args[2])) {
                        $this->writeActionLog($this->currentPlayer . " " . trim($args[1]) . " " . trim($args[2]));
                    } else {
                        $this->writeActionLog($this->currentPlayer . " " . trim($args[1]));
                    }

                    $this->writeAccessLog(__CLASS__ . '::' . $value . 'Action');
                    call_user_func([$this, $value . 'Action']);
                }
            }
        }
    }

	protected function isValidAction($actionName, $args)
	{
		$this->writeAccessLog(__METHOD__);

		if (strtolower(trim($args[1])) === strtolower($actionName) || trim($args[1]) === substr($actionName, 0, 1)) {
			if (call_user_func([$this, $actionName . 'ActionCheck'], $args)) {
				$this->writeAccessLog(__CLASS__ . '::' . $actionName . 'ActionCheck');
				return true;
			}
		}
		return false;
	}

    protected function takeActionCheck($args)
    {
        if (!isset($args[2]) || trim($args[2]) == '') {
        	$this->writeErrorLog(__METHOD__ . '|| You haven\'t precised which item you wish to take!');
            echo "You haven't precised which item you wish to take!\n";
            return false;
        }

	    $itemUtils = new ItemUtils();
	    $itemName = str_replace('_', ' ', trim($this->args[2]));
	    if (!$itemUtils->isItemExist($itemName, $this->em)) {
		    $this->writeErrorLog(__METHOD__ . '|| The item ' . $itemName . ' does not exist.');
            return false;
        }

	    $itemName = str_replace('_', ' ', trim($this->args[2]));
	    $item = $this->em->find('RPGManager\Entity\Item', $itemUtils->getItemId($itemName, $this->em));

	    $characterUtils = new CharacterUtils();
	    $player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));

	    if (!in_array($item, $itemUtils->getItemsInArea($player->getLocation()))) {
		    $this->writeErrorLog(__METHOD__ . '|| The item ' . $itemName . ' is not accessible.');
            echo "This item is not accessible.\n";
            return false;
	    }

        return true;
    }

	protected function takeAction()
	{
		$characterUtils = new CharacterUtils();
		$player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));

		$itemUtils = new ItemUtils();
		$itemName = str_replace('_', ' ', trim($this->args[2]));
		$item = $this->em->find('RPGManager\Entity\Item', $itemUtils->getItemId($itemName, $this->em));

		// add item in player inventory
		if ($itemUtils->isItemInInventory($item->getId(), $this->em)) {
			$inventories = $player->getCharacterInventories();
			foreach ($inventories as $inventory) {
				if ($inventory->getItem()->getId() == $item->getId()) {
					$inventory->setNumber($inventory->getNumber() + 1);
					$this->em->persist($inventory);
					$this->em->flush();
				}
			}
		} else {
			$characterInventory[$this->currentPlayer . '_' . $itemName] = new CharacterInventory();
			$characterInventory[$this->currentPlayer . '_' . $itemName]->setCharacter($player);
			$characterInventory[$this->currentPlayer . '_' . $itemName]->setItem($item);
			$characterInventory[$this->currentPlayer . '_' . $itemName]->setNumber(1);

			$this->em->persist($characterInventory[$this->currentPlayer . '_' . $itemName]);
			$this->em->flush();
		}

		echo "Item " . $itemName . " has been added to your inventory! \n";

		// remove item from place
		$itemLocations = $player->getLocation()->getItemLocations();
		foreach ($itemLocations as $location) {
			if ($location->getItem()->getId() == $itemUtils->getItemId($itemName, $this->em)) {
				if ($location->getNumber() > 1) {
					$location->setNumber($location->getNumber() - 1);
					$this->em->persist($location);
				} else {
					$this->em->remove($location);
				}
				$this->em->flush();
			}
		}
	}

    protected function dropActionCheck($args)
    {
        if (!isset($args[2]) || trim($args[2]) == '') {
	        $this->writeErrorLog(__METHOD__ . '|| You haven\'t precised which item you wish to drop!');
            echo "You haven't precised which item you wish to drop!\n";
            return false;
        }

        $itemUtils = new ItemUtils();
        $itemName = str_replace('_', ' ', trim($this->args[2]));
        $item = $this->em->find('RPGManager\Entity\Item', $itemUtils->getItemId($itemName, $this->em));
        
        if (!$itemUtils->isItemInInventory($item, $this->em)) {
	        $this->writeErrorLog(__METHOD__ . '|| The item ' . $itemName . ' is not in your inventory.');
            echo "This item is not in your inventory\n";
            return false;
        }
        
        return true;
    }

    protected function dropAction()
    {
        $characterUtils = new CharacterUtils();
        $player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));

        $itemUtils = new ItemUtils();
        $itemName = str_replace('_', ' ', trim($this->args[2]));
        $locationName = $player->getLocation()->getName();
        $item = $this->em->find('RPGManager\Entity\Item', $itemUtils->getItemId($itemName, $this->em));

        // remove item from inventory
        $inventories = $player->getCharacterInventories();
        foreach ($inventories as $inventory) {
            if ($inventory->getItem()->getId() == $itemUtils->getItemId($itemName, $this->em)) {
                if ($inventory->getNumber() > 1) {
                    $inventory->setNumber($inventory->getNumber() - 1);
                    $this->em->persist($inventory);
                } else {
                    $this->em->remove($inventory);
                }
                $this->em->flush();
            }
        }
        
        echo "Item " . $itemName . " has been dropped from your inventory! \n";

        // add item in location
        if (in_array($item, $itemUtils->getItemsInArea($player->getLocation()))) {
            $itemLocations = $player->getLocation()->getItemLocations();
            foreach ($itemLocations as $location) {
                if ($location->getItem()->getId() == $item->getId()) {
                    $location->setNumber($location->getNumber() + 1);
                    $this->em->persist($location);
                    $this->em->flush();
                }
            }
        } else {
            $ItemLocation[$locationName . '_' . $itemName] = new ItemLocation();
            $ItemLocation[$locationName . '_' . $itemName]->setPlace($player->getLocation());
            $ItemLocation[$locationName . '_' . $itemName]->setItem($item);
            $ItemLocation[$locationName . '_' . $itemName]->setNumber(1);

            $this->em->persist($ItemLocation[$locationName . '_' . $itemName]);
            $this->em->flush();
        }
    }

    protected function moveActionCheck($args)
    {
        if (!isset($args[2]) || trim($args[2]) == '') {
	        $this->writeErrorLog(__METHOD__ . '|| You haven\'t precised in which place you wish to go!');
            echo "You haven't precised in which place you wish to go!\n";
            return false;
        }

        $characterUtils = new CharacterUtils();
        $player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));

        $playerDestination = null;
        foreach ($player->getLocation()->getDirections() as $direction) {
            if (strtolower($direction->getName()) == strtolower(trim($this->args[2]))) {
                $playerDestination = $direction;
                break;
            }
        }

        if ($playerDestination === null) {
	        $this->writeErrorLog(__METHOD__ . '|| This place hasn\'t any direction named ' . trim($this->args[2]));
            echo "This place hasn't any direction named " . trim($this->args[2]) . ".\n";
            return false;
        }

        return true;
    }

    protected function moveAction()
    {
	    $characterUtils = new CharacterUtils();
	    $player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));

        $playerDestination = null;
        foreach ($player->getLocation()->getDirections() as $direction) {
            if (strtolower($direction->getName()) == strtolower(trim($this->args[2]))) {
                $playerDestination = $direction;
                break;
            }
        }

        $player->setLocation($playerDestination->getPlaceArrival());

        $this->em->persist($player);
        $this->em->flush();

        $this->locationAction();
    }

    protected function attackActionCheck($args)
    {
	    $monsterUtils = new MonsterUtils();
	    $characterUtils = new CharacterUtils();
	    $player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));

        if (empty($monsterUtils->getFoes($player->getLocation()))) {
	        $this->writeErrorLog(__METHOD__ . '|| Lol, there\'s no one to attack here.');
            echo "Lol, there's no one to attack here.\n";
            return false;
        }

        return true;
    }

    protected function attackAction()
    {
	    $monsterUtils = new MonsterUtils();
	    $characterUtils = new CharacterUtils();
	    $player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));
        $location = $player->getLocation();
        $this->em->refresh($location);

	    $fight = new FightMode($characterUtils->getCharactersInArea($location), $monsterUtils->getFoes($location), $this->em);
        $fight->startFight();
    }

    protected function locationActionCheck($args)
    {
        return true;
    }

    protected function locationAction()
    {
	    $characterUtils = new CharacterUtils();
	    $player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));
        echo "\n" . $player->getLocation()->getName() . ': ' . $player->getLocation()->getDescription() . "\n\n";

        $location = $player->getLocation();
        $this->em->refresh($location);

        $this->displayDirections();

        $monsterUtils = new MonsterUtils();
	    $monsterUtils->displayMonsters($location);

	    $npcUtils = new NpcUtils();
	    $npcUtils->displayNpcs($location);

	    $itemUtils = new ItemUtils();
	    $itemUtils->displayItems($location);
    }

	private function speakActionCheck($args) {
		if (!isset($args[2]) || trim($args[2]) == '') {
			$this->writeErrorLog(__METHOD__ . '|| You haven\'t precised with who you wish to speak!');
			echo "You haven't precised with who you wish to speak!\n";
			return false;
		}

		$npcUtils = new NpcUtils();
		$npcName = str_replace('_', ' ', trim($this->args[2]));
		if (!$npcUtils->isNpcExist($npcName, $this->em)) {
			return false;
		}

		$npc = $this->em->find('RPGManager\Entity\Npc', $npcUtils->getNpcId($npcName, $this->em));
		$characterUtils = new CharacterUtils();
		$player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));

		if (!in_array($npc, $npcUtils->getNpcsInArea($player->getLocation()))) {
			$this->writeErrorLog(__METHOD__ . '|| There is no npc with the name ' . $npcName);
			echo "There is no npc with this name. \n";
			return false;
		}

		return true;
	}

	private function speakAction() {
		$npcUtils = new NpcUtils();
		$npcName = str_replace('_', ' ', trim($this->args[2]));

		$npc = $this->em->find('RPGManager\Entity\Npc', $npcUtils->getNpcId($npcName, $this->em));
		echo "\n" . $npc->getName() . ": \"" .$npc->getDialog() . "\" \n";
	}

    private function caracActionCheck($args)
    {
        return true;
    }

    private function caracAction()
    {
	    $characterUtils = new CharacterUtils();
	    $player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));
        $this->em->refresh($player);
        $playerStats = $player->getStats();
        $playerInventory= $player->getCharacterInventories();
        $statList = [];

        foreach ($playerInventory as $item) {
            foreach ($item->getItem()->getItemStats() as $stat) {
                if (!isset($statList[$stat->getStat()->getName()])) {
                    $statList[$stat->getStat()->getName()] = $stat->getStat()->getValue() * $item->getNumber();
                } else {
                    $statList[$stat->getStat()->getName()] += $stat->getStat()->getValue() * $item->getNumber();
                }
            }
        }

        foreach ($playerStats as $stat) {
            if (!isset($statList[$stat->getStat()->getName()])) {
                $statList[$stat->getStat()->getName()] = $stat->getStat()->getValue();
            } else {
                $statList[$stat->getStat()->getName()] += $stat->getStat()->getValue();
            }
        }

        echo "\n";
        foreach ($statList as $name => $value) {
            echo "• " . $name . " " . $value . "\n";
        }
    }

    private function displayDirections()
    {
	    $characterUtils = new CharacterUtils();
	    $player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));
        $directions = $player->getLocation()->getDirections();

        echo "• Available direction(s) :";
        foreach ($directions as $direction) {
            echo "\n - " . $direction->getName();
        }
        echo "\n";
    }

}

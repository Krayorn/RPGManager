<?php

namespace RPGManager\Model;

use RPGManager\Entity\CharacterInventory;
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

        $game->writeAccessLog("startGame()");
        $game->setEntityManager($entityManager);
        $game::$settings = $settings;

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
        $this->writeAccessLog("executePlayerAction()");
        $this->currentPlayer = trim($args[0]);

        if ($this->isPlayerExist()) {
            foreach ($availableActions as $value) {
                if ($this->isValidAction($value, $args)) {
                    if (isset($args[2])) {
                        $this->writeActionLog($this->currentPlayer . " " . trim($args[1]) . " " . trim($args[2]));
                    } else {
                        $this->writeActionLog($this->currentPlayer . " " . trim($args[1]));
                    }
                    $this->writeAccessLog($value . "Action");
                    call_user_func([$this, $value . "Action"]);
                }
            }
        }
    }

	protected function isValidAction($actionName, $args)
	{
		if (strtolower(trim($args[1])) === strtolower($actionName) || trim($args[1]) === substr($actionName, 0, 1)) {
			if (call_user_func([$this, $actionName . "ActionCheck"], $args)) {
				$this->writeAccessLog($actionName . "ActionCheck");
				return true;
			}
		}
		return false;
	}

    protected function takeActionCheck($args)
    {
        if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
            return false;
        }

	    $itemUtils = new ItemUtils();
	    $itemName = str_replace('_', ' ', trim($this->args[2]));
	    if (!$itemUtils->isItemExist($itemName, $this->em)) {
            return false;
        }

	    $itemName = str_replace('_', ' ', trim($this->args[2]));
	    $item = $this->em->find('RPGManager\Entity\Item', $itemUtils->getItemId($itemName, $this->em));
	    $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());

	    if (!in_array($item, $itemUtils->getItemsInArea($player->getLocation()))) {
            echo "This item is not accessible.\n";
            return false;
	    }

        return true;
    }

	protected function takeAction()
	{
		$player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());

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

		echo "Item " . $itemName . " added to your inventory! \n";

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

    protected function moveActionCheck($args)
    {
        if (!isset($args[2]) || trim($args[2]) == '') {
            echo "You haven't precised in which place you wish to go !\n";
            return false;
        }

        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());

        $playerDestination = null;
        foreach ($player->getLocation()->getDirections() as $direction) {
            if (strtolower($direction->getName()) == strtolower(trim($this->args[2]))) {
                $playerDestination = $direction;
                break;
            }
        }

        if ($playerDestination === null) {
            echo "This place hasn't any direction named: " . trim($this->args[2]) . ".\n";
            return false;
        }

        return true;
    }

    protected function moveAction()
    {
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());

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
	    $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
	    $monsterUtils = new MonsterUtils();
	    
        if (empty($monsterUtils->getFoes($player->getLocation()))) {
            echo "Lol, there's no one to attack here \n";
            return false;
        }

        return true;
    }

    protected function attackAction()
    {
	    $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
	    $monsterUtils = new MonsterUtils();
	    
        $fight = new FightMode($this->getCharactersInArea(), $monsterUtils->getFoes($player->getLocation()), $this->em);
        $fight->startFight();
    }

    protected function locationActionCheck($args)
    {
        return true;
    }

    protected function locationAction()
    {
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        echo "\n";
        echo $player->getLocation()->getName() . ': ' . $player->getLocation()->getDescription() . "\n\n";
        
        $location = $player->getLocation();
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
			echo "ARGS MISSING \n";
			return false;
		}

		$npcUtils = new NpcUtils();
		$npcName = str_replace('_', ' ', trim($this->args[2]));
		if (!$npcUtils->isNpcExist($npcName, $this->em)) {
			return false;
		}

		$npc = $this->em->find('RPGManager\Entity\Npc', $npcUtils->getNpcId($npcName, $this->em));
		$player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
		if (!in_array($npc, $npcUtils->getNpcsInArea($player->getLocation()))) {
			echo "There is no npc with this name. \n";
			return false;
		}

		return true;
	}

	private function speakAction() {
		$npcUtils = new NpcUtils();
		$npcName = str_replace('_', ' ', trim($this->args[2]));
		
		$npc = $this->em->find('RPGManager\Entity\Npc', $npcUtils->getNpcId($npcName, $this->em));
		echo $npc->getDialog() . "\n";
	}

    private function caracActionCheck($args)
    {
        return true;
    }

    private function caracAction()
    {
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        $playerStats = $player->getStats();
        $playerInventory= $player->getCharacterInventories();
        $statList = [];

        foreach ($playerInventory as $item) {
            foreach ($item->getItem()->getItemStats() as $stat) {
                if (!isset($statList[$stat->getStat()->getName()])) {
                    $statList[$stat->getStat()->getName()] = $stat->getStat()->getValue();
                } else {
                    $statList[$stat->getStat()->getName()] += $stat->getStat()->getValue();
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
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        $directions = $player->getLocation()->getDirections();
        echo "• Available direction(s) :";
        foreach ($directions as $direction) {
            echo "\n - " . $direction->getName();
        }
        echo "\n";
    }

	private function getCharactersInArea()
	{
		$player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
		$playerLocations = $player->getLocation()->getCharacters();
		$players = [];

		foreach ($playerLocations as $character) {
			array_push($players, $character);
		}

		return $players;
	}

}

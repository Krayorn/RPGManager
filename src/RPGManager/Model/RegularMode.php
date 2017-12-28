<?php

namespace RPGManager\Model;

use RPGManager\Entity\CharacterInventory;

class RegularMode extends Game
{

    private static $instance = null;
    protected $basicActions = ["move", "take", "inventory", "location", "attack", "speak"];

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
        echo "IN TAKE ACTION CHECK \n";

        if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
            return false;
        }

        if (!$this->isItemExists()) {
            return false;
        }

	    $item = $this->em->find('RPGManager\Entity\Item', $this->getItemId());
	    if (!in_array($item, $this->getItemsInArea())) {
            echo "THIS ITEM IS NOT ACCESSIBLE FROM THIS AREA.";
            return false;
	    }

        return true;
    }

    private function isItemExists()
    {
        $itemName = str_replace('_', ' ', trim($this->args[2]));

        $result = $this->em->createQueryBuilder()
            ->select('item.name')
            ->from('RPGManager\Entity\Item', 'item')
            ->where('item.name = :name')
            ->setParameter('name', $itemName)
            ->getQuery()
            ->getResult();

        if (empty($result) || null == $result) {
            echo "THIS ITEM DOES NOT EXIST. \n";
            return false;
        }

        return true;
    }

    protected function takeAction()
    {
        $itemName = str_replace('_', ' ', trim($this->args[2]));
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        $item = $this->em->find('RPGManager\Entity\Item', $this->getItemId());

        $characterInventory[$this->currentPlayer . '_' . $itemName] = new CharacterInventory();
        $characterInventory[$this->currentPlayer . '_' . $itemName]->setCharacter($player);
        $characterInventory[$this->currentPlayer . '_' . $itemName]->setItem($item);

        $this->em->persist($characterInventory[$this->currentPlayer . '_' . $itemName]);
        $this->em->flush();
        echo 'Item ' . $itemName . ' added to your inventory!';

	    $itemLocations = $player->getLocation()->getItemLocations();
	    foreach ($itemLocations as $location) {
	    	if ($location->getItem()->getId() == $this->getItemId()) {
	    		$this->em->remove($location);
	    		$this->em->flush();
		    }
	    }

    }

    private function getItemId()
    {
        $itemName = str_replace('_', ' ', trim($this->args[2]));

        $itemId = $this->em->createQueryBuilder()
            ->select('item.id')
            ->from('RPGManager\Entity\Item', 'item')
            ->where('item.name = :name')
            ->setParameter('name', $itemName)
            ->getQuery()
            ->getResult();

        return $itemId[0]['id'];
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
            if ($direction->getName() == trim($this->args[2])) {
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
            if ($direction->getName() == trim($this->args[2])) {
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
        echo "IN ATTACK ACTION CHECK \n";
        if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
        }
        return true;
    }

    protected function attackAction()
    {
        echo "IN ATTACK ACTION \n";
        $fight = new FightMode($this->getCharactersInArea(), $this->getMonstersInArea());
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

        $this->displayDirections();
        $this->displayMonsters();
        $this->displayNpcs();
        $this->displayItems();
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

    private function displayMonsters()
    {
        $monsters = $this->getMonstersInArea();
        $numberOfMonsters = $this->getNumbersOfMonstersInArea();
        if (empty($monsters)) {
            echo "• Ennemy in this place : There's no threat here.";
        } else {
            echo "• Monster(s) in this place :";
            $c = 0;
            foreach ($monsters as $monster){
                echo "\n - " . $monster->getName() . "(" . $numberOfMonsters[$c] . ")";
                $c++;
            }
        }
        echo "\n";
    }

    private function displayNpcs()
    {
        $npcs = $this->getNpcsInArea();
        if (empty($npcs)) {
            echo "• Npc(s) in this place : There's no one here.";
        } else {
            echo "• Npc(s) in this place :";
            foreach ($npcs as $npc) {
                echo "\n - " . $npc->getName() . " : " . $npc->getDescription();
            }
        }
        echo "\n";
    }

    private function displayItems()
    {
        $items = $this->getItemsInArea();
        if (empty($items)) {
            echo "• Item(s) in this place : There's no item(s) here.";
        } else {
            echo "• Item(s) in this place :";
            foreach ($items as $item) {
                echo "\n - " . $item->getName() . " : " . $item->getDescription();
            }
        }
        echo "\n";
    }

	private function getMonstersInArea()
	{
		$player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
		$monsterLocations = $player->getLocation()->getMonsterLocations();
		$monsters = [];

		foreach ($monsterLocations as $location) {
			array_push($monsters, $location->getMonster());
		}

		return $monsters;
	}

    private function getNumbersOfMonstersInArea()
    {
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        $monsterLocations = $player->getLocation()->getMonsterLocations();
        $numberOfMonsters = [];

        foreach ($monsterLocations as $location) {
            array_push($numberOfMonsters, $location->getNumber());
        }
        return $numberOfMonsters;
    }

    private function getFoes()
    {
        $monsters = $this->getMonstersInArea();
        $numberOfMonsters = $this->getNumbersOfMonstersInArea();
        $foes = [];

        $c = 0;
        foreach ($monsters as $monster){
            for ($i = 0; $i < $numberOfMonsters[$c]; $i++){
                array_push($foes, $monster->getName());
            }
            $c++;
        }

        return $foes;
    }

	private function getNpcsInArea()
	{
		$player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
		$npcLocations = $player->getLocation()->getNpcLocations();
		$npcs = [];

		foreach ($npcLocations as $location) {
			array_push($npcs, $location->getNpc());
		}

		return $npcs;
	}

	private function getItemsInArea()
	{
		$player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
		$itemLocations = $player->getLocation()->getItemLocations();
		$items = [];

		foreach ($itemLocations as $location) {
			array_push($items, $location->getItem());
		}

		return $items;
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

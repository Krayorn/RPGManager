<?php

namespace RPGManager\Model;

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

    public static function startGame($entityManager)
    {
        $game = RegularMode::getInstance();
        $game->writeAccessLog("startGame()");
        $game->setEntityManager($entityManager);

        while (true) {
            echo "\nAVAILABLE ACTIONS:\n";
            $actions = $game->basicActions;
            foreach ($actions as $value) {
                echo $value . "  ";
            }
            echo "\n >> ";

            $handle = fopen("php://stdin","r");
            $line = fgets($handle);
            $args = explode(" ", $line);
            $game->setArgs($args);
            $game->executePlayerAction($args, $actions);
        }
    }

    protected function isArgValid($availableActions, $args)
    {
        if (!in_array(trim($args[1]), $availableActions)) {
            echo "COMMAND NOT VALID";
            return false;
        }
        return true;
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

        // TODO: check of item is in the area of the player

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
			->getResult()
		;

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
		    ->getResult()
	    ;

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
        foreach($player->getLocation()->getDirections() as $direction) {
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
        foreach($player->getLocation()->getDirections() as $direction) {
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

    protected function inventoryActionCheck()
    {
        return true;
    }

    protected function inventoryAction()
    {
	    $playerInventory = $this->em->createQueryBuilder()
		    ->select('item')
		    ->from('RPGManager\Entity\Item', 'item')
		    ->innerJoin('RPGManager\Entity\CharacterInventory', 'inventory', 'WITH', 'item.id = inventory.item')
		    ->where('inventory.character = :playerId')
		    ->setParameter('playerId', $this->getPlayerId())
		    ->getQuery()
		    ->getResult()
	    ;

	    if (empty($playerInventory)) {
	    	echo "Inventory is empty. \n";
	    } else {
		    foreach ($playerInventory as $playerItem) {
			    echo $playerItem->getName() . ': ' . $playerItem->getDescription() . "\n";
		    }
	    }
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
        $fight = new FightMode($this->currentPlayer, $this->getCharactersInArea(), $this->getFoesInArea());
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
    }

    private function displayDirections(){
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        $directions = $player->getLocation()->getDirections();
        echo "• Available directions :";
        foreach ($directions as $direction){
            echo " - " . $direction->getName();
        }
        echo "\n";
    }

    private function displayMonsters(){
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        $playerLocationId = $player->getLocation()->getId();
        $monsters = $this->em->createQueryBuilder()
            ->select('monster')
            ->from('RPGManager\Entity\Monster', 'monster')
            ->innerJoin('RPGManager\Entity\MonsterLocation', 'location', 'WITH', 'monster.id = location.monster')
            ->where('location.place = :monsterLocationId')
            ->setParameter('monsterLocationId', $playerLocationId)
            ->getQuery()
            ->getResult()
        ;
        if (empty($monsters)) {
            echo "• Ennemy in this place : There's no threat here. \n";
        } else {
            echo "• Ennemy in this place :";
            foreach ($monsters as $monster){
                echo " - " . $monster->getName();
            }
        }
        echo "\n";
    }

    private function getCharactersInArea()
    {

    }

    private function getNpcsInArea()
    {

    }

    private function getFoesInArea()
    {

    }
}

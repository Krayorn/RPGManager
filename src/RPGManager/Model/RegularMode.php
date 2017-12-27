<?php

namespace RPGManager\Model;

class RegularMode extends Game
{

    private static $instance = null;
    protected $basicActions = ["move", "take", "inventory", "location"];

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
            $actions = $game->getAvailableActions();
            echo "\nAVAILABLE ACTIONS:\n";
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
	    echo "IN MOVE ACTION CHECK \n";
	    if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
        }
        return true;
    }

    protected function moveAction()
    {
        echo "IN MOVE ACTION \n";
    }

    protected function inventoryActionCheck()
    {
        echo "IN INVENTORY ACTION CHECK \n";

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
	    echo "IN LOCATION ACTION CHECK \n";
        return true;
    }

    protected function locationAction()
    {
        echo "IN LOCATION ACTION \n";
    }

    private function getCharactersInArea()
    {

    }

    private function getFoesInArea()
    {

    }
}

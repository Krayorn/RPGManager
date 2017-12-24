<?php

namespace RPGManager\Model;

use Doctrine\ORM\Query\ResultSetMapping;
use RPGManager\Template;

class Game extends Template
{
    private static $instance = null;
    private $basicActions = ["move", "take", "inventory"];
    private $currentPlayer;
    private $em;


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Game();
        }
        return self::$instance;
    }

    public static function startGame($entityManager)
    {
        $game = Game::getInstance();
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
            $game->executePlayerAction($args, $actions);
        }
    }
    
    private function setEntityManager($entityManager) {
    	$this->em = $entityManager;
    }

    private function getAvailableActions()
    {
        $this->writeAccessLog("getAvailableActions()");

        $actions = $this->basicActions;
        if ($this->isSomeoneInArea()) {
            array_push($actions, 'attack');
        }
        return $actions;
    }

    private function isSomeoneInArea()
    {
        // TODO check monster and npc in area
        return true;
    }

    private function executePlayerAction($args, $availableActions)
    {
        $this->writeAccessLog("executePlayerAction()");
	    $this->currentPlayer = trim($args[0]);
	
	    if ($this->isPlayerExists() && $this->isArgValid($availableActions, $args)) {
	        foreach ($availableActions as $value) {
		        if ($this->isValidAction($value, $args)) {
			        $this->writeActionLog($this->currentPlayer . " " . trim($args[1]) . " " . trim($args[2]));
			        $this->writeAccessLog($value . "Action");
			        call_user_func([$this, $value . "Action"]);
		        }
	        }
        }
    }

    private function isArgValid($availableActions, $args)
    {
        if (!in_array(trim($args[1]), $availableActions)) {
            echo "COMMAND NOT VALID";
            return false;
        }
        return true;
    }
    
    private function isPlayerExists() {
	    $result = $this->em->createQueryBuilder()
		    ->select('player.name')
		    ->from('RPGManager\Entity\Character', 'player')
		    ->where('player.name = :name')
		    ->setParameter('name', $this->currentPlayer)
		    ->getQuery()
		    ->getResult()
	    ;
	    
	    if (empty($result) || null == $result) {
	    	echo "THIS PLAYER DOES NOT EXIST. \n";
		    return false;
	    }
	    return true;
    }

    private function isValidAction($actionName, $args)
    {
        if (strtolower(trim($args[1])) === strtolower($actionName) || trim($args[1])  === substr($actionName, 0, 1)) {
            if (call_user_func([$this, strtolower(trim($args[1])) . "ActionCheck"], $args)) {
                $this->writeAccessLog(strtolower(trim($args[1])) . "ActionCheck");
                return true;
            }
        }
        return false;
    }

    private function takeActionCheck($args)
    {
	    echo "IN TAKE ACTION CHECK \n";
	    if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
        }
        return true;
    }

    private function takeAction()
    {
        echo "IN TAKE ACTION \n";
    }

    private function moveActionCheck($args)
    {
	    echo "IN MOVE ACTION CHECK \n";
	    if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
        }
        return true;
    }

    private function moveAction()
    {
        echo "IN MOVE ACTION \n";
    }

    private function inventoryActionCheck()
    {
        echo "IN INVENTORY ACTION CHECK \n";

        return true;
    }

    private function inventoryAction()
    {
        echo "IN INVENTORY ACTION \n";
	    echo "FOR " . $this->currentPlayer;
    }

    private function attackActionCheck($args)
    {
	    echo "IN ATTACK ACTION CHECK \n";
	    if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
        }
        return true;
    }

    private function attackAction()
    {
        echo "IN ATTACK ACTION \n";
        $fight = new FightMode($this->currentPlayer, $this->getCharactersInArea(), $this->getFoesInArea());
        $fight->startFight();
    }

    private function getCharactersInArea()
    {

    }

    private function getFoesInArea()
    {

    }

}

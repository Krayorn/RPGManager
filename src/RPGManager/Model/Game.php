<?php

namespace RPGManager\Model;

use RPGManager\Entity\CharacterInventory;
use RPGManager\Template;

abstract class Game extends Template
{
    protected $currentPlayer;
    protected $em;
    protected $args;

    protected function setArgs($args)
    {
    	$this->args = $args;
    }

    protected function setEntityManager($entityManager)
    {
    	$this->em = $entityManager;
    }

    protected function getAvailableActions()
    {
        $this->writeAccessLog("getAvailableActions()");

        $actions = $this->basicActions;
        if ($this->isSomeoneInArea()) {
            array_push($actions, 'attack');
        }
        return $actions;
    }

    protected function isSomeoneInArea()
    {
        // TODO check monster and npc in area
        return true;
    }

    protected function executePlayerAction($args, $availableActions)
    {
        $this->writeAccessLog("executePlayerAction()");
	    $this->currentPlayer = trim($args[0]);

	    if ($this->isPlayerExist() && $this->isArgValid($availableActions, $args)) {
	        foreach ($availableActions as $value) {
		        if ($this->isValidAction($value, $args)) {
			        $this->writeActionLog($this->currentPlayer . " " . trim($args[1]) . " " . trim($args[2]));
			        $this->writeAccessLog($value . "Action");
			        call_user_func([$this, $value . "Action"]);
		        }
	        }
        }
    }

    protected function isPlayerExist()
    {
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

    protected function getPlayerId()
    {
	    $playerId = $this->em->createQueryBuilder()
		    ->select('player.id')
		    ->from('RPGManager\Entity\Character', 'player')
		    ->where('player.name = :name')
		    ->setParameter('name', $this->currentPlayer)
		    ->getQuery()
		    ->getResult()
	    ;

	    return $playerId[0]['id'];
    }

    protected function isValidAction($actionName, $args)
    {
        if (strtolower(trim($args[1])) === strtolower($actionName) || trim($args[1])  === substr($actionName, 0, 1)) {
            if (call_user_func([$this, strtolower(trim($args[1])) . "ActionCheck"], $args)) {
                $this->writeAccessLog(strtolower(trim($args[1])) . "ActionCheck");
                return true;
            }
        }
        return false;
    }

}

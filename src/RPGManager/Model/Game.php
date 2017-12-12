<?php

namespace RPGManager\Model;

use RPGManager\Template;

class Game extends Template
{
    private static $instance = null;
    private $basicActions = ["move", "take", "inventory"];
    private $currentPlayer;


    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Game();
        }
        return self::$instance;
    }

    public static function startGame()
    {
        $game = Game::getInstance();
        while (true) {
            $actions = $game->getAvailableActions();
            foreach ($actions as $key => $value) {
                echo $key . ") " . $value . "   ";
            }
            $handle = fopen("php://stdin","r");
            $line = fgets($handle);
            $args = explode(" ", $line);
            $game->executePlayerAction($args, $actions);
        }
    }

    private function getAvailableActions()
    {
    	$actions = $this->basicActions;
    	if ($this->isSomeoneInArea()) {
    		array_push($actions, 'attack');
	    }
        return $actions;
    }
	
	private function isSomeoneInArea()
	{
    	// check monster and npc in area
		return true;
	}

    private function executePlayerAction($args, $availableActions)
    {
        $this->currentPlayer = trim($args[0]);
        foreach ($availableActions as $value) {
            if ($this->isValidAction(trim($args[1]), $value)) {
                call_user_func([$this, $value . "Action"]);
            }
        }
    }

    private function isValidAction($userAction, $actionName)
    {
        return strtolower($userAction) === strtolower($actionName)
            ? true
            : $userAction === substr($actionName, 0, 1)
                ? true
                : false
        ;
    }
	
    private function takeAction()
    {
        echo "IN TAKE ACTION \n";
    }

    private function moveAction()
    {
        echo "IN MOVE ACTION \n";
    }

    private function inventoryAction()
    {
        echo "IN INVENTORY ACTION \n";
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


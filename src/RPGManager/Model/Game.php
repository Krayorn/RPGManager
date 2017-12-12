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

        $game->writeAccessLog("startGame()");

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
        // check monster and npc in area
        return true;
    }

    private function executePlayerAction($args, $availableActions)
    {
        $this->writeAccessLog("executePlayerAction()");

        $this->currentPlayer = trim($args[0]);

        $this->isArgValid($availableActions, $args);

        foreach ($availableActions as $value) {
            if ($this->isValidAction($value, $args)) {

                $this->writeActionLog($this->currentPlayer . " " . trim($args[1]) . " " . trim($args[2]));
                $this->writeAccessLog($value . "Action");

                call_user_func([$this, $value . "Action"]);
            }
        }
    }

    private function isArgValid($availableActions, $args)
    {
        if (!in_array(trim($args[1]), $availableActions)) {
            echo "COMMAND NOT VALID";
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
        if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
        }
        echo "IN TAKE ACTION CHECK";
        return true;
    }

    private function takeAction()
    {
        echo "IN TAKE ACTION \n";
    }

    private function moveActionCheck()
    {
        if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
        }
        echo "IN MOVE ACTION CHECK";

        return true;
    }

    private function moveAction()
    {
        echo "IN MOVE ACTION \n";
    }

    private function inventoryActionCheck()
    {
        echo "IN INVENTORY ACTION CHECK";

        return true;
    }

    private function inventoryAction()
    {
        echo "IN INVENTORY ACTION \n";
    }

    private function attackActionCheck()
    {
        if (!isset($args[2]) || trim($args[2]) == '') {
            echo "ARGS MISSING";
        }
        echo "IN ATTACK ACTION CHECK";

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

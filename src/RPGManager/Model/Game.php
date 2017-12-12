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
        foreach ($availableActions as $value) {
            if ($this->isValidAction(trim($args[1]), $value)) {

                $this->writeActionLog($this->currentPlayer . " " . trim($args[1]) . " " . trim($args[2]));

                call_user_func([$this, $value . "Action"]);
            }
        }
    }

    private function isValidAction($userAction, $actionName)
    {
        if (strtolower($userAction) === strtolower($actionName) || $userAction === substr($actionName, 0, 1)) {
            if (call_user_func([$this, strtolower($userAction) . "ActionCheck"])) {
                return true;
            }
        }
        return false;
    }

    private function takeActionCheck()
    {
        $this->writeAccessLog("takeActionCheck()");

        echo "IN TAKE ACTION CHECK";

        return true;
    }

    private function takeAction()
    {
        $this->writeAccessLog("takeAction()");

        echo "IN TAKE ACTION \n";
    }

    private function moveActionCheck()
    {
        $this->writeAccessLog("moveActionCheck()");

        echo "IN MOVE ACTION CHECK";

        return true;
    }

    private function moveAction()
    {
        $this->writeAccessLog("moveAction()");

        echo "IN MOVE ACTION \n";
    }

    private function inventoryActionCheck()
    {
        $this->writeAccessLog("inventoryActionCheck()");

        echo "IN INVENTORY ACTION CHECK";

        return true;
    }

    private function inventoryAction()
    {
        $this->writeAccessLog("takeAction()");

        echo "IN INVENTORY ACTION \n";
    }

    private function attackActionCheck()
    {
        $this->writeAccessLog("attackActionCheck()");

        echo "IN ATTACK ACTION CHECK";

        return true;
    }

    private function attackAction()
    {
        $this->writeAccessLog("attackAction()");

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

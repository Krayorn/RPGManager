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

        $line = "startGame()";
        $game->writeAccessLog($line);

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
        $line = "getAvailableActions()";
        $this->writeAccessLog($line);

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
        $line = "executePlayerAction()";
        $this->writeAccessLog($line);

        $this->currentPlayer = trim($args[0]);
        foreach ($availableActions as $value) {
            if ($this->isValidAction(trim($args[1]), $value)) {

                $line = $this->currentPlayer . " " . trim($args[1]) . " " . trim($args[2]);
                $this->writeActionLog($line);

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

    private function takeActionCheck() {
        $line = "takeActionCheck()";
        $this->writeAccessLog($line);

        echo "IN TAKE ACTION CHECK";

        return true;
    }

    private function takeAction()
    {
        $line = "takeAction()";
        $this->writeAccessLog($line);

        echo "IN TAKE ACTION \n";
    }

    private function moveActionCheck() {
        $line = "moveActionCheck()";
        $this->writeAccessLog($line);

        echo "IN MOVE ACTION CHECK";

        return true;
    }

    private function moveAction()
    {
        $line = "moveAction()";
        $this->writeAccessLog($line);

        echo "IN MOVE ACTION \n";
    }

    private function inventoryActionCheck() {
        $line = "inventoryActionCheck()";
        $this->writeAccessLog($line);

        echo "IN INVENTORY ACTION CHECK";

        return true;
    }

    private function inventoryAction()
    {
        $line = "takeAction()";
        $this->writeAccessLog($line);

        echo "IN INVENTORY ACTION \n";
    }

    private function attackActionCheck() {
        $line = "attackActionCheck()";
        $this->writeAccessLog($line);

        echo "IN ATTACK ACTION CHECK";

        return true;
    }

    private function attackAction()
    {
        $line = "attackAction()";
        $this->writeAccessLog($line);

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

<?php

namespace RPGManager\Utils;

use RPGManager\Template;

class Game extends Template {
    private static $instance = null;
    private $basicActions = ["move", "take", "inventory"];
    private $currentPlayer;


    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Game();
        }
        return self::$instance;
    }

    public static function startGame() {
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

    private function getAvailableActions() {
        return $this->basicActions;
    }

    private function executePlayerAction($args, $availableActions) {
        $this->currentPlayer = trim($args[0]);
        foreach ($availableActions as $value) {
            if ($this->isValidAction(trim($args[1]), $value)) {
                call_user_func([$this, $value . "Action"]);
            }
        }
    }

    private function isValidAction($userAction, $actionName) {
        return strtolower($userAction) === strtolower($actionName)
            ? true
            : $userAction === substr($actionName, 0, 1)
                ? true
                : false
        ;
    }

    private function TakeAction() {
        echo "IN TAKE ACTION \n";
    }

    private function MoveAction() {
        echo "IN MOVE ACTION \n";
    }

    private function InventoryAction() {
        echo "IN INVENTORY ACTION \n";
    }
}


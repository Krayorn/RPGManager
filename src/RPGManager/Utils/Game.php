<?php

namespace RPGManager\Utils;

use RPGManager\Template;

class Game extends Template {
    private static $instance = null;
    private $basicActions = ["Move", "Take", "Inventory"];

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
            $handle = fopen ("php://stdin","r");
            $line = fgets($handle);
            $args = explode(" ", $line);
            foreach ($actions as $key => $value) {
                if(trim($args[0]) == $key || trim($args[0]) == $value) {
                    call_user_func(array($game, $value . "Action"));
                }
            }
        }
    }

    private function getAvailableActions() {
        return $this->basicActions;
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


<?php

namespace RPGManager\Model;

use RPGManager\Template;

class FightMode extends Template
{
    private $attacker;
    private $players;
    private $foes;
    private $basicActions = ['flee', 'skills', 'inventory'];

    public function __construct($attacker, $players, $foes)
    {
        $this->attacker = $attacker;
        $this->players = $players;
        $this->foes = $foes;
    }

    public function startFight()
    {
        // $initative = $this->setInitiative();

        $line = "startFight()";
        $this->writeAccessLog($line);

        while (true) {
            $actions = $this->getAvailableFightActions();
            echo "\nAVAILABLE ACTIONS:\n";
            foreach ($actions as $value) {
                echo $value . "  ";
            }
            echo "\n >> ";

            $handle = fopen("php://stdin","r");
            $line = fgets($handle);
            $args = explode(" ", $line);
            $this->executeAttackerAction($args, $actions);
        }
    }

    private function setInitiative()
    {
        // get turns number == sum of $players number and $foes number
        // set order of turns
        // return $initiative = [1, 'attackerName'];
    }

    private function getAvailableFightActions()
    {
        $line = "getAvailableFightActions()";
        $this->writeAccessLog($line);

        return $this->basicActions;
    }

    private function executeAttackerAction($args, $availableActions)
    {
        $line = "executeAttackerAction()";
        $this->writeAccessLog($line);

        foreach ($availableActions as $value) {
            if ($this->isValidAction(trim($args[0]), $value)) {

                $line = $this->attacker . " " . trim($args[1]) . " " . trim($args[2]);
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

    private function fleeActionCheck() {
        $line = "fleeActionCheck()";
        $this->writeAccessLog($line);

        echo "IN fleeAction CHECK \n";

        return true;
    }

    private function fleeAction()
    {
        $line = "fleeAction()";
        $this->writeAccessLog($line);

        echo "IN FLEE ACTION \n";
    }

    private function skillsActionCheck() {
        $line = "skillsActionCheck()";
        $this->writeAccessLog($line);

        echo "IN skillsAction CHECK \n";

        return true;
    }

    private function skillsAction()
    {
        $line = "skillsAction()";
        $this->writeAccessLog($line);

        echo "IN SKILLS ACTION \n";
    }

    private function inventoryActionCheck() {
        $line = "inventoryActionCheck()";
        $this->writeAccessLog($line);

        echo "IN inventoryAction CHECK \n";

        return true;
    }

    private function inventoryAction()
    {
        $line = "inventoryAction()";
        $this->writeAccessLog($line);

        echo "IN INVENTORY ACTION \n";
    }

}

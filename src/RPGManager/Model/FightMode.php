<?php

namespace RPGManager\Model;

class FightMode extends Game
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
        $this->writeAccessLog("startFight()");

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

    protected function isArgValid($availableActions, $args)
    {
        if (!in_array(trim($args[0]), $availableActions)) {
            echo "COMMAND NOT VALID";
            return false;
        }
        return true;
    }

    private function setInitiative()
    {
        // get turns number == sum of $players number and $foes number
        // set order of turns
        // return $initiative = [1, 'attackerName'];
    }

    private function getAvailableFightActions()
    {
        $this->writeAccessLog("getAvailableFightActions()");

        return $this->basicActions;
    }

    private function executeAttackerAction($args, $availableActions)
    {
        $this->writeAccessLog("executeAttackerAction()");

        foreach ($availableActions as $value) {
            if ($this->isValidAction(trim($args[0]), $value)) {

                $this->writeActionLog($this->attacker . " " . trim($args[1]) . " " . trim($args[2]));
                $this->writeAccessLog($value . "Action");

                call_user_func([$this, $value . "Action"]);
            }
        }
    }

    private function fleeActionCheck()
    {
        echo "IN fleeAction CHECK \n";
        return true;
    }

    private function fleeAction()
    {
        echo "IN FLEE ACTION \n";
    }

    private function skillsActionCheck()
    {
        echo "IN skillsAction CHECK \n";

        return true;
    }

    private function skillsAction()
    {

        echo "IN SKILLS ACTION \n";
    }

    private function inventoryActionCheck()
    {
        echo "IN inventoryAction CHECK \n";

        return true;
    }

    private function inventoryAction()
    {
        echo "IN INVENTORY ACTION \n";
    }

}

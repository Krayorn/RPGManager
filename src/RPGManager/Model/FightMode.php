<?php

namespace RPGManager\Model;

class FightMode extends Game
{
    private $players;
    private $foes;
    private $basicActions = ['flee', 'skills', 'inventory'];

    public function __construct($players, $foes)
    {
        $this->players = $players;
        $this->foes = $foes;
    }

    public function startFight()
    {
        $initative = $this->setInitiative();
        $this->writeAccessLog("startFight()");

        while (true) {
            foreach($this->fighters as $fighter) {
                $this->currentFighter = $fighter;
                if (in_array($fighter, $this->foes)) {
                    $this->resolveMonsterTurn();
                } else {
                    $this->resolvePlayerTurn();
                }
            }
        }
    }

    private function sortByCarac($a, $b) {
        foreach($a->getStats() as $stat) {
            if ($stat->getStat()->getName() === $this::$settings['initiativeCarac']) {
                $aValue = $stat->getStat()->getValue();
            }
        }

        foreach($b->getStats() as $stat) {
            if ($stat->getStat()->getName() === $this::$settings['initiativeCarac']) {
                $bValue = $stat->getStat()->getValue();
            }
        }

        return ($bValue < $aValue) ? -1 : 1;
    }

    private function setInitiative()
    {

        $fighters = array_merge($this->players, $this->foes);

        if($this::$settings['initiativeCarac'] !== null) {
            usort($fighters, array($this, 'sortByCarac'));
        } else {
            shuffle($fighters);
        }
        $this->fighters = $fighters;

        foreach($fighters as $fighter) {
            echo $fighter->getName();
        }

        echo "\n";
    }

    private function getAvailableFightActions()
    {
        $this->writeAccessLog("getAvailableFightActions()");

        return $this->basicActions;
    }

    private function resolveMonsterTurn() {
        echo $this->currentFighter->getName() . " just made a move !\n";
        return true;
    }

    private function resolvePlayerTurn() {

        echo "it's " . $this->currentFighter->getName() . " turn ! \n";

        $actions = $this->getAvailableFightActions();
        echo "\nAVAILABLE ACTIONS:\n";
        foreach ($actions as $value) {
            echo $value . "  ";
        }
        echo "\n >> ";

        $handle = fopen("php://stdin","r");
        $line = fgets($handle);
        $args = explode(" ", $line);
        $this->executePlayerAction($args, $actions);
    }

    protected function executePlayerAction($args, $availableActions)
    {
        $this->writeAccessLog("executeAttackerAction()");

        $actionDone = false;
        foreach ($availableActions as $value) {
            if ($this->isValidAction($value, $args)) {
                $actionDone = true;

                $this->writeActionLog($this->currentFighter->getName() . " " . trim($args[0]));
                $this->writeAccessLog($value . "Action");

                call_user_func([$this, $value . "Action"]);
            }
        }

        if(!$actionDone) {
            if ($this->isASpell($args)) {

                $actionDone = true;

                $this->writeActionLog($this->currentFighter->getName() . " use " . trim($args[0]) . " on " . trim($args[1]));
                $this->writeAccessLog($value . "Action");
            }
        }

        if(!$actionDone) {
            echo "We don't know what you just did, plz use something that make sense you moron \n";
            $this->resolvePlayerTurn();
        }

    }

    private function isValidAction($actionName, $args)
    {
        if (strtolower(trim($args[0])) === strtolower($actionName) || trim($args[0]) === substr($actionName, 0, 1)) {
            if (call_user_func([$this, $actionName . "ActionCheck"], $args)) {
                $this->writeAccessLog($actionName . "ActionCheck");
                return true;
            }
        }
        return false;
    }

    protected function isASpell($args) {
        echo trim($args[0]) . "\n";
        $spells = $this->currentFighter->getCharacterSpells();

        foreach($spells as $spell) {
            echo $spell->getSpell()->getName() . "\n";
            if (trim($args[0]) == $spell->getSpell()->getName()) {
                return true;
            }
        }

        return false;
    }

    private function fleeActionCheck($args)
    {
        echo "IN fleeAction CHECK \n";
        return true;
    }

    private function fleeAction()
    {
        unset($this->fighters[array_search($this->currentFighter, $this->fighters)]);

        //TODO: check if characters in fight
        // if not end fight
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
}

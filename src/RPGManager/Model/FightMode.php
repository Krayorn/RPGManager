<?php

namespace RPGManager\Model;

class FightMode extends Game
{
    private $players;
    private $foes;
    private $fighters;
    private $basicActions = ['flee', 'skills', 'inventory'];
    private $currentFighter;
    private $currentTarget;
    private $currentSpell;

    public function __construct($players, $foes, $entityManager)
    {
        $this->players = $players;
        $this->foes = $foes;
        $this->setEntityManager($entityManager);
    }

    public function startFight()
    {
        $initative = $this->setInitiative();
        $this->writeAccessLog("startFight()");

        while (true) {
            foreach ($this->fighters as $fighter) {
                $this->currentFighter = $fighter;
                if (in_array($fighter, $this->foes)) {
                    $this->resolveMonsterTurn();
                }
                if (in_array($fighter, $this->players)) {
                    $this->resolvePlayerTurn();
                }
            }
        }
    }

    private function sortByCarac($a, $b)
    {
        foreach ($a->getStats() as $stat) {
            if ($stat->getStat()->getName() === $this::$settings['initiativeCarac']) {
                $aValue = $stat->getStat()->getValue();
            }
        }

        foreach ($b->getStats() as $stat) {
            if ($stat->getStat()->getName() === $this::$settings['initiativeCarac']) {
                $bValue = $stat->getStat()->getValue();
            }
        }

        return ($bValue < $aValue) ? -1 : 1;
    }

    private function setInitiative()
    {

        $fighters = array_merge($this->players, $this->foes);

        if ($this::$settings['initiativeCarac'] !== null) {
            usort($fighters, array($this, 'sortByCarac'));
        } else {
            shuffle($fighters);
        }
        $this->fighters = $fighters;

        foreach ($fighters as $fighter) {
            echo $fighter->getName();
        }

        echo "\n";
    }

    private function getAvailableFightActions()
    {
        $this->writeAccessLog("getAvailableFightActions()");

        return $this->basicActions;
    }

    private function resolveMonsterTurn()
    {
        echo $this->currentFighter->getName() . " just made a move !\n";
        return true;
    }

    private function resolvePlayerTurn()
    {
        $this->displayFoesState();
        echo "\nIt's " . $this->currentFighter->getName() . " turn ! \n";

        $actions = $this->getAvailableFightActions();
        echo "\nAVAILABLE ACTIONS:\n";
        foreach ($actions as $value) {
            echo $value . "  ";
        }
        echo "\n >> ";

        $handle = fopen("php://stdin", "r");
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
                $this->currentPlayer = $this->currentFighter->getName();

                $this->writeActionLog($this->currentFighter->getName() . " " . trim($args[0]));
                $this->writeAccessLog($value . "Action");

                call_user_func([$this, $value . "Action"]);

                if (in_array($value, ['skills', 'inventory'])) {
                    $this->resolvePlayerTurn();
                }
            }
        }

<<<<<<< HEAD
        if(!$actionDone) {
            if ($this->isASpell(trim($args[0])) && $this->isTargetValid(trim($args[1]))) {
=======
        if (!$actionDone) {
            if ($this->isASpell($args)) {
>>>>>>> 0d9ac04a150785c2235874d6d8ea1d34537d4876
                $actionDone = true;

                $this->writeActionLog($this->currentFighter->getName() . " use " . $this->currentSpell->getName() . " on " . $this->currentTarget->getName());
                $this->writeAccessLog($value . "Action");

                call_user_func([$this, "execute" . $this->currentSpell->getType() . "Spell"]);
            }
        }

        if (!$actionDone) {
            echo "We don't know what you just did, plz use something that make sense you moron \n";
            $this->resolvePlayerTurn();
        }

    }

<<<<<<< HEAD
    private function executeDamageSpell()
    {
        $damages = $this->currentSpell->getSpellStats();
        $statToMinus = $this::$settings['StatForHealth'];

        foreach($this->currentTarget->getStats() as $stat) {
            if ($stat->getStat()->getName() === $statToMinus) {
                echo $this->currentTarget->getName() . " is going to be hurt on " . $statToMinus . "\n";
                echo "Current Value: " . $stat->getStat()->getValue() . "\n";

                foreach($damages as $damage) {
                    $damage = $damage->getStat();

                    $stat->getStat()->setValue($stat->getStat()->getValue() - $damage->getValue());
                    echo $this->currentTarget->getName() . " took " . $damage->getValue() . " " . $damage->getName() . "\n";
                }
=======
    private function execureFighterSpell($spellName, $targetName)
    {
>>>>>>> 0d9ac04a150785c2235874d6d8ea1d34537d4876

                echo "Remaining Value Value: " . $stat->getStat()->getValue() . "\n";

                if ($stat->getStat()->getValue() <= 0) {
                        unset($this->fighters[array_search($this->currentTarget, $this->fighters)]);
                        unset($this->foes[array_search($this->currentTarget, $this->foes)]);
                }
            }
        }
    }

    private function executeDebuffSpell()
    {
        echo "in DebuffSpell \n";
    }

    private function executeBuffSpell()
    {
        echo "in BuffSpell \n";
    }

    private function executeHealSpell()
    {
        echo "in HealSpell \n";
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

<<<<<<< HEAD
    protected function isASpell($spellName)
=======
    protected function isASpell($args)
>>>>>>> 0d9ac04a150785c2235874d6d8ea1d34537d4876
    {

        $spellName = str_replace('_', ' ', $spellName);

        $spells = $this->currentFighter->getCharacterSpells();

        foreach ($spells as $spell) {
            if ($spellName == $spell->getSpell()->getName()) {
                $this->currentSpell = $spell->getSpell();
                return true;
            }
        }

        return false;
    }

    private function isTargetValid($targetName)
    {
        $targetName = str_replace('_', ' ', $targetName);

        foreach($this->fighters as $fighter) {
            if ($targetName === $fighter->getName()) {
                $spellType = $this->currentSpell->getType();
                if ((in_array($fighter, $this->foes) && ($spellType == 'debuff' || $spellType == 'damage'))
                    ||(in_array($fighter, $this->players) && ($spellType == 'heal' || $spellType == 'buff'))) {
                        $this->currentTarget = $fighter;
                        return true;
                }
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
        $spells = $this->currentFighter->getCharacterSpells();
        foreach ($spells as $spell) {
            echo $spell->getSpell()->getName() . ": " . $spell->getSpell()->getDescription() . "\n";
        }
    }

    private function displayFoesState()
    {
        $foes = $this->foes;
        foreach ($foes as $foe) {
            echo "\n- " . $foe->getName();
            $foeStats = $foe->getStats();
            foreach ($foeStats as $foeStat) {
                echo " | " . $foeStat->getStat()->getName() . " " . $foeStat->getStat()->getValue();
            }
        }
        echo "\n";
    }
}

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

        $this->fillTemporaryStatsArray();
    }

    private function fillTemporaryStatsArray()
    {
        foreach ($this->players as $player) {
            $temporaryStats = [];
            foreach($player->getStats() as $stat){
                $temporaryStats[$stat->getStat()->getName()] = $stat->getStat()->getValue();
            }
            $player->setTemporaryStats($temporaryStats);
        }

        foreach ($this->foes as $foe) {
            $temporaryStats = [];
            foreach($foe->getStats() as $stat){
                $temporaryStats[$stat->getStat()->getName()] = $stat->getStat()->getValue();
            }
            $foe->setTemporaryStats($temporaryStats);
        }
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
            if ($stat->getStat()->getName() === $this::$settings['statForInititative']) {
                $aValue = $stat->getStat()->getValue();
            }
        }

        foreach ($b->getStats() as $stat) {
            if ($stat->getStat()->getName() === $this::$settings['statForInititative']) {
                $bValue = $stat->getStat()->getValue();
            }
        }

        return ($bValue < $aValue) ? -1 : 1;
    }

    private function setInitiative()
    {

        $fighters = array_merge($this->players, $this->foes);

        if ($this::$settings['statForInititative'] !== null) {
            usort($fighters, array($this, 'sortByCarac'));
        } else {
            shuffle($fighters);
        }
        $this->fighters = $fighters;
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

        if(!$actionDone) {
            if ($this->isASpell(trim($args[0])) && $this->isTargetValid(trim($args[1]))) {
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

    private function executeDamageSpell()
    {
        $damages = $this->currentSpell->getSpellStats();
        $statToMinus = $this::$settings['statForHealth'];

        echo $this->currentTarget->getName() . " is going to be hurt on " . $statToMinus . "\n";
        echo "Current Value: " . $this->currentTarget->getTemporaryStats()[$statToMinus] . "\n";

        foreach($damages as $damage) {
            $damage = $damage->getStat();

            $stats = $this->currentTarget->getTemporaryStats();

            $stats[$statToMinus] = $stats[$statToMinus] - $damage->getValue();
            echo $this->currentTarget->getName() . " took " . $damage->getValue() . " " . $damage->getName() . "\n";
        }

        $this->currentTarget->setTemporaryStats($stats);
        echo "Remaining Value Value: " .$stats[$statToMinus] . "\n";

        if ($this->currentTarget->getTemporaryStats()[$statToMinus] <= 0) {
                echo "unset";
                unset($this->fighters[array_search($this->currentTarget, $this->fighters)]);
                unset($this->foes[array_search($this->currentTarget, $this->foes)]);
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

    protected function isASpell($spellName)
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
            foreach ($foe->getTemporaryStats() as $key => $foeStat) {
                echo " | " . $key . " " . $foeStat;
            }
        }
        echo "\n";
    }
}

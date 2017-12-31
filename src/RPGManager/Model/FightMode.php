<?php

namespace RPGManager\Model;

class FightMode extends Game
{
    private $players;
    private $foes;
    private $fighters;
    private $deadFoes;
    private $playersOut = [];
    private $location;
    private $basicActions = ['flee', 'skills', 'inventory'];
    private $currentFighter;
    private $currentTarget;
    private $currentSpell;

    public function __construct($players, $foes, $entityManager)
    {
        $this->players = $players;
        $this->foes = $foes;
        $this->setEntityManager($entityManager);
        $this->location = $players[0]->getLocation();

        $this->fillTemporaryStatsArray();
    }

    private function fillTemporaryStatsArray()
    {
	    $this->writeAccessLog(__METHOD__);

        foreach ($this->players as $player) {
            $playerInventory= $player->getCharacterInventories();
            $temporaryStats = [];
            foreach($player->getStats() as $stat){
                $temporaryStats[$stat->getStat()->getName()] = $stat->getStat()->getValue();

                if($stat->getStat()->getName() == $this::$settings['statForHealth']) {
                    $temporaryStats['hpToSave'] = $stat->getStat()->getValue();
                }
            }

            foreach ($playerInventory as $item) {
                foreach ($item->getItem()->getItemStats() as $stat) {
                    $temporaryStats[$stat->getStat()->getName()] =  $temporaryStats[$stat->getStat()->getName()] + $stat->getStat()->getValue();
                }
            }

            $player->setTemporaryStats($temporaryStats);
        }

        foreach ($this->foes as $foe) {
            $monsterInventory= $foe->getMonsterInventories();
            $temporaryStats = [];
            foreach($foe->getStats() as $stat){
                $temporaryStats[$stat->getStat()->getName()] = $stat->getStat()->getValue();
            }

            foreach ($monsterInventory as $item) {
                foreach ($item->getItem()->getItemStats() as $stat) {
                    $temporaryStats[$stat->getStat()->getName()] =  $temporaryStats[$stat->getStat()->getName()] + $stat->getStat()->getValue();
                }
            }

            $foe->setTemporaryStats($temporaryStats);
        }
    }

    public function startFight()
    {
        $this->writeAccessLog(__METHOD__);

        while (true) {
            $this->setInitiative();

            echo "\n--- NEW TURN ---\n";

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
	    $this->writeAccessLog(__METHOD__);

        $aValue = $a->getTemporaryStats()[$this::$settings['statForInititative']];
        $bValue = $b->getTemporaryStats()[$this::$settings['statForInititative']];

        return ($bValue < $aValue) ? -1 : 1;
    }

    private function setInitiative()
    {
	    $this->writeAccessLog(__METHOD__);

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
        $this->writeAccessLog(__METHOD__);

        return $this->basicActions;
    }

    private function resolveMonsterTurn()
    {
	    $this->writeAccessLog(__METHOD__);

        $spells = $this->currentFighter->getSpells();

        $this->currentSpell = $spells[rand(0, count($spells) - 1)]->getSpell();
        $this->currentTarget = $this->currentSpell->getType() === 'buff'
        ? $this->foes[rand(0, count($this->foes) - 1)]
        : $this->players[rand(0, count($this->players) - 1)];

        echo "\n" . $this->currentFighter->getName() . " used " . $this->currentSpell->getName() . " on " . $this->currentTarget->getName() . "\n";

        call_user_func([$this, "execute" . $this->currentSpell->getType() . "Spell"]);
    }

    private function resolvePlayerTurn()
    {
	    $this->writeAccessLog(__METHOD__);

        $this->displayFightState();
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
        $this->writeAccessLog(__METHOD__);

        $actionDone = false;
        foreach ($availableActions as $value) {
            if ($this->isValidAction($value, $args)) {
                $actionDone = true;
                $this->currentPlayer = $this->currentFighter->getName();

                $this->writeActionLog($this->currentFighter->getName() . " " . trim($args[0]));
                $this->writeAccessLog(__CLASS__ . '::' . $value . 'Action');
                call_user_func([$this, $value . 'Action']);

                if (in_array($value, ['skills', 'inventory'])) {
                    $this->resolvePlayerTurn();
                }
            }
        }

        if (!$actionDone) {
            if ($this->isASpell(trim($args[0])) && $this->isTargetValid(trim($args[1]))) {
                $actionDone = true;

                $this->writeActionLog($this->currentFighter->getName() . " used " . $this->currentSpell->getName() . " on " . $this->currentTarget->getName());
                $this->writeAccessLog(__CLASS__ . '::' . $value . 'Action');

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
	    $this->writeAccessLog(__METHOD__);

        $damages = $this->currentSpell->getSpellStats();
        $statToMinus = $this::$settings['statForHealth'];

        foreach($damages as $damage) {
            $damage = $damage->getStat();

            $stats = $this->currentTarget->getTemporaryStats();

            $stats[$statToMinus] = $stats[$statToMinus] - $damage->getValue();
            echo $this->currentTarget->getName() . " took " . $damage->getValue() . " " . $damage->getName() . "\n";
        }
        $this->currentTarget->setTemporaryStats($stats);

        if ($this->currentTarget->getTemporaryStats()[$statToMinus] <= 0) {

            if(in_array($this->currentTarget, $this->players)) {

                $statToSave = $this->currentTarget->getTemporaryStats();
                $statToSave['hpToSave'] = 0;
                $this->currentTarget->setTemporaryStats($statToSave);

                array_push($this->playersOut, $this->currentTarget);

                unset($this->players[array_search($this->currentTarget, $this->players)]);
                $this->players = array_values($this->players);
            } else {
                if (isset($this->deadFoes[$this->foes[array_search($this->currentTarget, $this->foes)]->getId()])) {
                    $this->deadFoes[$this->foes[array_search($this->currentTarget, $this->foes)]->getId()] += 1;
                } else {
                    $this->deadFoes[$this->foes[array_search($this->currentTarget, $this->foes)]->getId()] = 1;
                }

                unset($this->foes[array_search($this->currentTarget, $this->foes)]);
                $this->foes = array_values($this->foes);
            }

            echo $this->currentTarget->getName()  . " died from his wound \n";
            unset($this->fighters[array_search($this->currentTarget, $this->fighters)]);

                if(count($this->foes) === 0) {
                    echo "\nAll foes have been defeated !\n";
                    $this->leaveFight();
                }
                if(count($this->players) === 0) {
                    echo "\nAll players have been defeated !\n";
                    $this->leaveFight();
                }
        }
    }

    private function executeDebuffSpell()
    {
	    $this->writeAccessLog(__METHOD__);

        $this->executeStatChangeSpell('debuff');
    }

    private function executeBuffSpell()
    {
	    $this->writeAccessLog(__METHOD__);

        $this->executeStatChangeSpell('buff');
    }

    private function executeStatChangeSpell($type)
    {
	    $this->writeAccessLog(__METHOD__);

        $statsToUpdate = $this->currentSpell->getSpellStats();
        $currentStats = $this->currentTarget->getTemporaryStats();

        foreach($statsToUpdate as $stat) {
            $stat = $stat->getStat();
            if($type === 'buff') {
                $currentStats[$stat->getName()] = $currentStats[$stat->getName()] + $stat->getValue();

                echo $this->currentTarget->getName() . " took a buff of +" . $stat->getValue() . " " . $stat->getName() . " points\n";
            } else {

                $currentStats[$stat->getName()] = $currentStats[$stat->getName()] - $stat->getValue();
                echo $this->currentTarget->getName() . " took a debuff of -" . $stat->getValue() . " " . $stat->getName() . " points\n";

                if ($currentStats[$stat->getName()] <= 0) {
                    $currentStats[$stat->getName()] = 1;
                }

            }
        }

        $this->currentTarget->setTemporaryStats($currentStats);
    }

    private function isValidAction($actionName, $args)
    {
	    $this->writeAccessLog(__METHOD__);

        if (strtolower(trim($args[0])) === strtolower($actionName) || trim($args[0]) === substr($actionName, 0, 1)) {
            if (call_user_func([$this, $actionName . 'ActionCheck()'], $args)) {
                $this->writeAccessLog(__CLASS__ . '::' . $actionName . 'ActionCheck');
                return true;
            }
        }
        return false;
    }

    protected function isASpell($spellName)
    {
	    $this->writeAccessLog(__METHOD__);

        $spellName = str_replace('_', ' ', $spellName);
        $spells = $this->currentFighter->getSpells();

        foreach ($spells as $spell) {
            if ( strtolower($spellName) == strtolower($spell->getSpell()->getName())) {
                $this->currentSpell = $spell->getSpell();
                return true;
            }
        }

        return false;
    }

    private function isTargetValid($targetName)
    {
	    $this->writeAccessLog(__METHOD__);

        $targetName = str_replace('_', ' ', $targetName);

        foreach($this->fighters as $fighter) {
            if (strtolower($targetName) === strtolower($fighter->getName())) {
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

    private function leaveFight()
    {
	    $this->writeAccessLog(__METHOD__);

        $monstersLocation = $this->location->getMonsterLocations();
        foreach ($monstersLocation as $monsterLocation) {
            $monsterId = $monsterLocation->getMonster()->getId();
            if (isset($this->deadFoes[$monsterId])) {
                if ($this->deadFoes[$monsterId] == $monsterLocation->getNumber()) {
                    $this->em->remove($monsterLocation);
                } else {
                    $monsterLocation->setNumber($monsterLocation->getNumber() - $this->deadFoes[$monsterId]);
                    $this->em->persist($monsterLocation);
                }
            }
        }

        foreach($this->playersOut as $player) {
            if ($player->getTemporaryStats()['hpToSave'] === 0) {
                $this->em->remove($player);
            } else {
                $stats = $player->getStats();
                foreach($stats as $stat) {
                    if ($stat->getStat()->getName() == $this::$settings['statForHealth']){

                        $newStat = $this->em->createQueryBuilder()
                        ->select('stat')
                        ->from('RPGManager\Entity\Stat', 'stat')
                        ->where('stat.value = :value AND stat.name = :name')
                        ->setParameters(['value' => $player->getTemporaryStats()['hpToSave'], 'name' => $this::$settings['statForHealth']])
                        ->getQuery()
                        ->getSingleResult();

                        $stat->setStat($newStat);
                    }
                }
                $this->em->persist($player);
            }
        }

        $this->em->flush();

        RegularMode::startGame($this->em, $this::$settings);
    }

    private function fleeActionCheck($args)
    {
        return true;
    }

    private function fleeAction()
    {
        $statToSave = $this->currentFighter->getTemporaryStats();
        if ($statToSave[$this::$settings['statForHealth']] < $statToSave['hpToSave']) {
            $statToSave['hpToSave'] = $statToSave[$this::$settings['statForHealth']];
        }
        $this->currentFighter->setTemporaryStats($statToSave);

        array_push($this->playersOut, $this->currentFighter);

        unset($this->fighters[array_search($this->currentFighter, $this->fighters)]);
        unset($this->players[array_search($this->currentFighter, $this->players)]);

        $this->players = array_values($this->players);

        if(count($this->players) === 0) {
            $this->leaveFight();
        }
    }

    private function skillsActionCheck()
    {
        echo "In skillsAction CHECK \n";

        return true;
    }

    private function skillsAction()
    {
        $spells = $this->currentFighter->getSpells();
        foreach ($spells as $spell) {
            echo $spell->getSpell()->getName() . ": " . $spell->getSpell()->getDescription() . "\n";
        }
    }

    private function displayFightState()
    {
	    $this->writeAccessLog(__METHOD__);

        $fighters = $this->fighters;
        foreach ($fighters as $fighter) {
            echo "\n- " . $fighter->getName();
            foreach ($fighter->getTemporaryStats() as $key => $fighterStat) {
                echo " | " . $key . " " . $fighterStat;
            }
        }
        echo "\n";
    }
}

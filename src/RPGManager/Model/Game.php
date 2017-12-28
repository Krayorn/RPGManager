<?php

namespace RPGManager\Model;

use RPGManager\Template;

abstract class Game extends Template
{
    protected $currentPlayer;
    protected $em;
    protected $args;

    protected function setArgs($args)
    {
        $this->args = $args;
    }

    protected function setEntityManager($entityManager)
    {
        $this->em = $entityManager;
    }

    protected function executePlayerAction($args, $availableActions)
    {
        $this->writeAccessLog("executePlayerAction()");
        $this->currentPlayer = trim($args[0]);

        // && $this->isArgValid($availableActions, $args) just commented, don't really get it, does the exact same thing as
        // isValidAction, but stop the use of lowerCase FirstLetter for BasicAction such as aeros i for aeros inventory
        // quite annoying TODO: Check if still needed, if not remove from FightMode AND RegularMode
        if ($this->isPlayerExist()) {
            foreach ($availableActions as $value) {
                if ($this->isValidAction($value, $args)) {
                    if (isset($args[2])) {
                        $this->writeActionLog($this->currentPlayer . " " . trim($args[1]) . " " . trim($args[2]));
                    } else {
                        $this->writeActionLog($this->currentPlayer . " " . trim($args[1]));
                    }
                    $this->writeAccessLog($value . "Action");
                    call_user_func([$this, $value . "Action"]);
                }
            }
        }
    }

    protected function isPlayerExist()
    {
        $result = $this->em->createQueryBuilder()
            ->select('player.name')
            ->from('RPGManager\Entity\Character', 'player')
            ->where('player.name = :name')
            ->setParameter('name', $this->currentPlayer)
            ->getQuery()
            ->getResult();

        if (empty($result) || null == $result) {
            echo "THIS PLAYER DOES NOT EXIST. \n";
            return false;
        }
        return true;
    }

    protected function getPlayerId()
    {
        $playerId = $this->em->createQueryBuilder()
            ->select('player.id')
            ->from('RPGManager\Entity\Character', 'player')
            ->where('player.name = :name')
            ->setParameter('name', $this->currentPlayer)
            ->getQuery()
            ->getResult();

        return $playerId[0]['id'];
    }

    protected function getPlayerLocationId(){
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        $playerLocationId = $player->getLocation()->getId();

        return $playerLocationId;
    }

    protected function isValidAction($actionName, $args)
    {
        if (strtolower(trim($args[1])) === strtolower($actionName) || trim($args[1]) === substr($actionName, 0, 1)) {
            if (call_user_func([$this, $actionName . "ActionCheck"], $args)) {
                $this->writeAccessLog($actionName . "ActionCheck");
                return true;
            }
        }
        return false;
    }

}

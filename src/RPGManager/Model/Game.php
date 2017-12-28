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

}

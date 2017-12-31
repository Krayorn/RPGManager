<?php

namespace RPGManager\Utils;

use RPGManager\Template;

class CharacterUtils
{
	public function isPlayerExist($currentPlayer, $em)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);
		
		$result = $em->createQueryBuilder()
			->select('player.name')
			->from('RPGManager\Entity\Character', 'player')
			->where('player.name = :name')
			->setParameter('name', $currentPlayer)
			->getQuery()
			->getResult()
		;

		if (empty($result) || null == $result) {
			echo "This player does not exist.\n";
			return false;
		}

		return true;
	}

	public function getPlayerId($currentPlayer, $em)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);
		
		$playerId = $em->createQueryBuilder()
			->select('player.id')
			->from('RPGManager\Entity\Character', 'player')
			->where('player.name = :name')
			->setParameter('name', $currentPlayer)
			->getQuery()
			->getResult()
		;

		return $playerId[0]['id'];
	}

	public function getCharactersInArea($location)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);
		
		$players = [];
		foreach ($location->getCharacters() as $character) {
			array_push($players, $character);
		}

		return $players;
	}
}

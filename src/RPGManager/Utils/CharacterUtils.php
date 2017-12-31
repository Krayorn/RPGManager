<?php

namespace RPGManager\Utils;

use RPGManager\Template;

class CharacterUtils
{
	public function isPlayerExist($currentPlayer, $em)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);
		
		$msc = microtime(true);
		
		$result = $em->createQueryBuilder()
			->select('player.name')
			->from('RPGManager\Entity\Character', 'player')
			->where('player.name = :name')
			->setParameter('name', $currentPlayer)
			->getQuery()
			->getResult()
		;
		
		$template->writeRequestLog(__METHOD__, microtime(true ) - $msc);
		
		if (empty($result) || null == $result) {
			$this->writeErrorLog(__METHOD__ . '|| The player ' . $currentPlayer . ' does not exist.');
			echo "This player does not exist.\n";
			return false;
		}
		
		return true;
	}
	
	public function getPlayerId($currentPlayer, $em)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);
		
		$msc = microtime(true);
		
		$playerId = $em->createQueryBuilder()
			->select('player.id')
			->from('RPGManager\Entity\Character', 'player')
			->where('player.name = :name')
			->setParameter('name', $currentPlayer)
			->getQuery()
			->getResult()
		;
		
		$template->writeRequestLog(__METHOD__, microtime(true ) - $msc);
		
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

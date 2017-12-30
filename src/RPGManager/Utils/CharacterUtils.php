<?php

namespace RPGManager\Utils;

class CharacterUtils
{
	public function isPlayerExist($currentPlayer, $em)
	{
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
		$players = [];
		foreach ($location->getCharacters() as $character) {
			array_push($players, $character);
		}
		
		return $players;
	}
}
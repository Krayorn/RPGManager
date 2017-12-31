<?php

namespace RPGManager\Utils;

use RPGManager\Template;

class NpcUtils
{
	public function getNpcId($npcName, $em)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);
		
		$npcId = $em->createQueryBuilder()
			->select('npc.id')
			->from('RPGManager\Entity\Npc', 'npc')
			->where('npc.name = :name')
			->setParameter('name', $npcName)
			->getQuery()
			->getResult()
		;
		
		return $npcId[0]['id'];
	}
	
	public function isNpcExist($npcName, $em)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);
		
		$result = $em->createQueryBuilder()
			->select('npc.name')
			->from('RPGManager\Entity\Npc', 'npc')
			->where('npc.name = :name')
			->setParameter('name', $npcName)
			->getQuery()
			->getResult()
		;
		
		if (empty($result) || null == $result) {
			echo "This npc does not exist. \n";
			return false;
		}
		
		return true;
	}
	
	public function displayNpcs($location)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);
		
		$npcs = $this->getNpcsInArea($location);
		
		if (empty($npcs)) {
			echo "• Npc(s) in this place : There's no one here.";
		} else {
			echo "• Npc(s) in this place :";
			foreach ($npcs as $npc) {
				echo "\n - " . $npc->getName() . " : " . $npc->getDescription();
			}
		}
		echo "\n";
	}
	
	public function getNpcsInArea($location)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);
		
		$npcs = [];
		foreach ($location->getNpcLocations() as $npcLocation) {
			array_push($npcs, $npcLocation->getNpc());
		}
		
		return $npcs;
	}
}
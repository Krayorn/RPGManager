<?php

namespace RPGManager\Utils;

class MonsterUtils
{
	public function displayMonsters($location)
	{
		$monsters = $this->getMonstersInArea($location);
		$numberOfMonsters = $this->getNumbersOfMonstersInArea($location);
		
		if (empty($monsters)) {
			echo "• Ennemy in this place : There's no threat here.";
		} else {
			echo "• Monster(s) in this place :";
			$c = 0;
			foreach ($monsters as $monster) {
				echo "\n - " . $monster->getName() . " (" . $numberOfMonsters[$c] . ")";
				$c ++;
			}
		}
		echo "\n";
	}
	
	public function getMonstersInArea($location)
	{
		$monsters = [];
		foreach ($location->getMonsterLocations() as $monsterLocation) {
			array_push($monsters, $monsterLocation->getMonster());
		}
		
		return $monsters;
	}
	
	public function getNumbersOfMonstersInArea($location)
	{
		$numberOfMonsters = [];
		foreach ($location->getMonsterLocations() as $monsterLocation) {
			array_push($numberOfMonsters, $monsterLocation->getNumber());
		}
		
		return $numberOfMonsters;
	}
	
	public function getFoes($location)
	{
		$monsters = $this->getMonstersInArea($location);
		$numberOfMonsters = $this->getNumbersOfMonstersInArea($location);
		$foes = [];
		
		$c = 0;
		foreach ($monsters as $monster){
			for ($i = 0; $i < $numberOfMonsters[$c]; $i++){
				if($numberOfMonsters[$c] > 1) {
					$foe = clone $monster;
					$foe->setName($monster->getName() . $i);
				} else {
					$foe = clone $monster;
				}
				array_push($foes, $foe);
			}
			$c++;
		}
		
		return $foes;
	}
}
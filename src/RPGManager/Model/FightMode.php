<?php

namespace RPGManager\Model;

class FightMode
{
	private $attacker;
	private $players;
	private $foes;
	private $basicActions = ['flee', 'skills', 'inventory'];
	
	public function __construct($attacker, $players, $foes)
	{
		$this->attacker = $attacker;
		$this->players = $players;
		$this->foes = $foes;
	}
	
	public function startFight()
	{
		// $initative = $this->setInitiative();
		
		while (true) {
			$actions = $this->getAvailableActions();
			foreach ($actions as $key => $value) {
				echo $key . ") " . $value . "   ";
			}
			$handle = fopen("php://stdin","r");
			$line = fgets($handle);
			$args = explode(" ", $line);
			$this->executeAttackerAction($args, $actions);
		}
	}
	
	private function setInitiative()
	{
		// get turns number == sum of $players number and $foes number
		// set order of turns
		// return $initiative = [1, 'attackerName'];
	}
	
	private function getAvailableActions()
	{
		return $this->basicActions;
	}
	
	private function executeAttackerAction($args, $availableActions)
	{
		$this->currentPlayer = trim($args[0]);
		foreach ($availableActions as $value) {
			if ($this->isValidAction(trim($args[1]), $value)) {
				call_user_func([$this, $value . "Action"]);
			}
		}
	}
	
	private function isValidAction($userAction, $actionName)
	{
		return strtolower($userAction) === strtolower($actionName)
			? true
			: $userAction === substr($actionName, 0, 1)
				? true
				: false
			;
	}

}
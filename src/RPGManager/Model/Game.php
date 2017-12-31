<?php

namespace RPGManager\Model;

use RPGManager\Template;
use RPGManager\Utils\CharacterUtils;

abstract class Game extends Template
{
    protected $currentPlayer;
    protected $em;
    protected $args;
    static protected $settings;

    protected function setArgs($args)
    {
	    $this->writeAccessLog(__METHOD__);

        $this->args = $args;
    }

    protected function setEntityManager($entityManager)
    {
	    $this->writeAccessLog(__METHOD__);

        $this->em = $entityManager;
    }

    protected function getPlayerLocationId()
    {
	    $this->writeAccessLog(__METHOD__);

        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        $playerLocationId = $player->getLocation()->getId();

        return $playerLocationId;
    }

	protected function inventoryActionCheck()
	{
		$this->writeAccessLog(__METHOD__);

		return true;
	}

	protected function inventoryAction()
	{
		$this->writeAccessLog(__METHOD__);

		$characterUtils = new CharacterUtils();
		$player = $this->em->find('RPGManager\Entity\Character', $characterUtils->getPlayerId($this->currentPlayer, $this->em));
        $this->em->refresh($player);
		$playerInventory = $player->getCharacterInventories();

		if (empty($playerInventory)) {
			echo "Inventory is empty. \n";
		} else {
			echo "\n";
			foreach ($playerInventory as $playerItem) {
				echo "• " . $playerItem->getItem()->getName() . ': '
					. $playerItem->getItem()->getDescription() . " (" . $playerItem->getNumber() . ")\n";
			}
		}
	}
}

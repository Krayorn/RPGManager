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
        $this->args = $args;
    }

    protected function setEntityManager($entityManager)
    {
        $this->em = $entityManager;
    }

    protected function getPlayerLocationId()
    {
        $player = $this->em->find('RPGManager\Entity\Character', $this->getPlayerId());
        $playerLocationId = $player->getLocation()->getId();

        return $playerLocationId;
    }

	protected function inventoryActionCheck()
	{
		return true;
	}

	protected function inventoryAction()
	{
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

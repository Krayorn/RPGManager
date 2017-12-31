<?php

namespace RPGManager\Utils;

use RPGManager\Template;

class ItemUtils
{
	public function isItemExist($itemName, $em)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);

		$msc = microtime(true);

		$result = $em->createQueryBuilder()
			->select('item.name')
			->from('RPGManager\Entity\Item', 'item')
			->where('item.name = :name')
			->setParameter('name', $itemName)
			->getQuery()
			->getResult()
		;

		$template->writeRequestLog(__METHOD__, microtime(true ) - $msc);

		if (empty($result) || null == $result) {
			$template->writeErrorLog(__METHOD__ . '|| The item ' . $itemName . ' does not exist.');
			echo "This item does not exist. \n";
			return false;
		}

		return true;
	}

	public function isItemInInventory($itemId, $playerId, $em)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);

		$msc = microtime(true);
		$result = $em->createQueryBuilder()
			->select('inventory')
			->from('RPGManager\Entity\CharacterInventory', 'inventory')
			->where('inventory.item = :item AND inventory.character =:player')
			->setParameters(['item' => $itemId, 'player' => $playerId])
			->getQuery()
			->getResult()
		;

		$template->writeRequestLog(__METHOD__, microtime(true ) - $msc);

		if (empty($result) || null == $result) {
			return false;
		}

		return true;
	}

	public function getItemId($itemName, $em)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);

		$msc = microtime(true);

		$itemId = $em->createQueryBuilder()
			->select('item.id')
			->from('RPGManager\Entity\Item', 'item')
			->where('item.name = :name')
			->setParameter('name', $itemName)
			->getQuery()
			->getResult()
		;

		$template->writeRequestLog(__METHOD__, microtime(true ) - $msc);

		return $itemId[0]['id'];
	}

	public function displayItems($location)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);

		$items = $this->getItemsInArea($location);

		if (empty($items)) {
			echo "• Item(s) in this place : There's no item(s) here.";
		} else {
			$numberOfItems = $this->getNumbersOfItemsInArea($location);

			echo "• Item(s) in this place :";
			$c = 0;
			foreach ($items as $item) {
				echo "\n - " . $item->getName() . " : "
					. $item->getDescription() . " (" . $numberOfItems[$c] . ")";
				$c ++;
			}
		}
		echo "\n";
	}

	public function getItemsInArea($location)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);

		$itemLocations = $location->getItemLocations();
		$items = [];

		foreach ($itemLocations as $location) {
			array_push($items, $location->getItem());
		}

		return $items;
	}

	public function getNumbersOfItemsInArea($location)
	{
		$template = new Template();
		$template->writeAccessLog(__METHOD__);

		$itemLocations = $location->getItemLocations();
		$numberOfItems = [];

		foreach ($itemLocations as $location) {
			array_push($numberOfItems, $location->getNumber());
		}
		return $numberOfItems;
	}
}

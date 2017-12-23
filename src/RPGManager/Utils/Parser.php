<?php

namespace RPGManager\Utils;

use RPGManager\Entity\ItemLocation;
use RPGManager\Entity\MonsterLocation;
use RPGManager\Entity\NpcLocation;
use RPGManager\Template;
use RPGManager\Entity\Stat;
use RPGManager\Entity\Item;
use RPGManager\Entity\ItemStat;
use RPGManager\Entity\Spell;
use RPGManager\Entity\SpellStat;
use RPGManager\Entity\Monster;
use RPGManager\Entity\MonsterStat;
use RPGManager\Entity\MonsterSpell;
use RPGManager\Entity\MonsterInventory;
use RPGManager\Entity\Npc;
use RPGManager\Entity\Place;
use RPGManager\Entity\Direction;
use RPGManager\Entity\Character;
use RPGManager\Entity\CharacterStat;
use RPGManager\Entity\CharacterSpell;
use RPGManager\Entity\CharacterInventory;

use Doctrine\ORM\EntityManager;

class Parser extends Template {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Parser();
        }
        return self::$instance;
    }

    public static function generateModelsDb($gameConfig, $settings, $entityManager) {
        $parser = Parser::getInstance();

        $parser->writeAccessLog("generateModelsDb()");

        $items = $gameConfig["items"];
        $monsters = $gameConfig["monsters"];
        $npcs = $gameConfig["npcs"];
        $characters = $gameConfig["characters"];
        $places = $gameConfig["places"];
        $stats = $gameConfig["stats"];
        $spells = $gameConfig["spells"];

        $statsEntities = [];

        foreach ($stats as $key => $stat) {
            for ($i = $stat["range"][0]; $i <= $stat["range"][1]; $i ++) {
                $statsEntities[$stat['name'] . '_' . $i] =  new Stat();
                $statsEntities[$stat['name'] . '_' . $i]->setName($stat['name']);
                $statsEntities[$stat['name'] . '_' . $i]->setValue($i);
                // TODO: condition for checking description exists
                $statsEntities[$stat['name'] . '_' . $i]->setDescription($stat['description']);

                $entityManager->persist($statsEntities[$stat['name'] . '_' . $i]);
            }
        }

        $itemsEntities = [];
        $itemsStatsTable = [];

        foreach ($items as $key => $item) {
            $itemsEntities[$item['name']] = new Item();
            $itemsEntities[$item['name']]->setName($item['name']);
            // TODO: condition for checking description exists
            $itemsEntities[$item['name']]->setDescription($item['description']);
            
            foreach ($item['stats'] as $stat) {
                $itemsStatsTable[$item['name'] . '_' . $stat['name']] = new ItemStat();
                $itemsStatsTable[$item['name'] . '_' . $stat['name']]->setItem($itemsEntities[$item['name']]);
                $itemsStatsTable[$item['name'] . '_' . $stat['name']]->setStat($statsEntities[$stat['name'] . '_' . $stat['value']]);

                $entityManager->persist($itemsStatsTable[$item['name'] . '_' . $stat['name']]);
            }
        }

        $spellsEntities = [];
        $spellsStatsTable = [];

        foreach ($spells as $key => $spell) {
            $spellsEntities[$spell['name']] = new Spell();
            $spellsEntities[$spell['name']]->setName($spell['name']);
            // TODO: condition for checking description exists
            $spellsEntities[$spell['name']]->setDescription($spell['description']);
            $spellsEntities[$spell['name']]->setType($spell['type']);
            
            foreach ($spell['stats'] as $stat) {
                $spellsStatsTable[$spell['name'] . '_' . $stat['name']] = new SpellStat();
                $spellsStatsTable[$spell['name'] . '_' . $stat['name']]->setSpell($spellsEntities[$spell['name']]);
                $spellsStatsTable[$spell['name'] . '_' . $stat['name']]->setStat($statsEntities[$stat['name'] . '_' . $stat['value']]);

                $entityManager->persist($spellsStatsTable[$spell['name'] . '_' . $stat['name']]);
            }
        }

        $monstersEntities = [];
        $monstersStatsTable = [];
        $monstersSpellsTable = [];
        $monstersInventoryTable = [];

        foreach ($monsters as $key => $monster) {
            $monstersEntities[$monster['name']] = new Monster();
            $monstersEntities[$monster['name']]->setName($monster['name']);
            // TODO: condition for checking description exists
            $monstersEntities[$monster['name']]->setDescription($monster['description']);

            foreach ($monster['stats'] as $stat) {
                $monstersStatsTable[$monster['name'] . '_' . $stat['name']] = new MonsterStat();
                $monstersStatsTable[$monster['name'] . '_' . $stat['name']]->setMonster($monstersEntities[$monster['name']]);
                $monstersStatsTable[$monster['name'] . '_' . $stat['name']]->setStat($statsEntities[$stat['name'] . '_' . $stat['value']]);

                $entityManager->persist($monstersStatsTable[$monster['name'] . '_' . $stat['name']]);
            }

            foreach ($monster['spells'] as $spell) {
                $monstersSpellsTable[$monster['name'] . '_' . $spell['name']] = new MonsterSpell();
                $monstersSpellsTable[$monster['name'] . '_' . $spell['name']]->setMonster($monstersEntities[$monster['name']]);
                $monstersSpellsTable[$monster['name'] . '_' . $spell['name']]->setSpell($spellsEntities[$spell['name']]);

                $entityManager->persist($monstersSpellsTable[$monster['name'] . '_' . $spell['name']]);
            }

            if (isset($monster['inventory'])) {
                foreach ($monster['inventory'] as $item) {
                    $monstersInventoryTable[$monster['name'] . '_' . $item['name']] = new MonsterInventory();
                    $monstersInventoryTable[$monster['name'] . '_' . $item['name']]->setMonster($monstersEntities[$monster['name']]);
                    $monstersInventoryTable[$monster['name'] . '_' . $item['name']]->setItem($itemsEntities[$item['name']]);

                    $entityManager->persist($monstersInventoryTable[$monster['name'] . '_' . $item['name']]);
                }
            }
        }

        $npcsEntities = [];

        foreach ($npcs as $key => $npc) {
            $npcsEntities[$npc['name']] = new Npc();
            $npcsEntities[$npc['name']]->setName($npc['name']);
            // TODO: condition for checking description exists
            $npcsEntities[$npc['name']]->setDescription($npc['description']);
            $npcsEntities[$npc['name']]->setDialog($npc['dialog']);

            $entityManager->persist($npcsEntities[$npc['name']]);
        }

        $placesEntities = [];
        $placesDirectionsTable = [];
	    $itemsLocationsTable = [];
	    $monstersLocationsTable = [];
	    $npcsLocationsTable = [];
	
	    foreach ($places as $key => $place) {
            $placesEntities[$place['name']] = new Place();
            $placesEntities[$place['name']]->setName($place['name']);
            // TODO: condition for checking description exists;
            $placesEntities[$place['name']]->setDescription($place['description']);

            $entityManager->persist($placesEntities[$place['name']]);
        }

        foreach ($places as $key => $place) {
            foreach ($place['directions'] as $direction) {
                $placesDirectionsTable[$place['name'] . '_' . $direction['directionName']] = new Direction();
                $placesDirectionsTable[$place['name'] . '_' . $direction['directionName']]->setName($direction['directionName']);
                $placesDirectionsTable[$place['name'] . '_' . $direction['directionName']]->setPlaceStart($placesEntities[$place['name']]);
                $placesDirectionsTable[$place['name'] . '_' . $direction['directionName']]->setPlaceArrival($placesEntities[$direction['placeArrival']]);

                $entityManager->persist($placesDirectionsTable[$place['name'] . '_' . $direction['directionName']]);
            }
            
            foreach ($place['items'] as $item) {
	            if (array_key_exists($item['name'], $itemsEntities)) {
		            $itemsLocationsTable[$place['name'] . '_' . $item['name']] = new ItemLocation();
		            $itemsLocationsTable[$place['name'] . '_' . $item['name']]->setItem($itemsEntities[$item['name']]);
		            $itemsLocationsTable[$place['name'] . '_' . $item['name']]->setNumber($item['number']);
		            $itemsLocationsTable[$place['name'] . '_' . $item['name']]->setPlace($placesEntities[$place['name']]);
		
		            $entityManager->persist($itemsLocationsTable[$place['name'] . '_' . $item['name']]);
	            }
            }
	
	        foreach ($place['monsters'] as $monster) {
		        if (array_key_exists($monster['name'], $monstersEntities)) {
			        $monstersLocationsTable[$place['name'] . '_' . $monster['name']] = new MonsterLocation();
			        $monstersLocationsTable[$place['name'] . '_' . $monster['name']]->setMonster($monstersEntities[$monster['name']]);
			        $monstersLocationsTable[$place['name'] . '_' . $monster['name']]->setNumber($monster['number']);
			        $monstersLocationsTable[$place['name'] . '_' . $monster['name']]->setPlace($placesEntities[$place['name']]);
			
			        $entityManager->persist($monstersLocationsTable[$place['name'] . '_' . $monster['name']]);
		        }
	        }
	
	        foreach ($place['npcs'] as $npc) {
		        if (array_key_exists($npc['name'], $npcsEntities)) {
			        $npcsLocationsTable[$place['name'] . '_' . $npc['name']] = new NpcLocation();
			        $npcsLocationsTable[$place['name'] . '_' . $npc['name']]->setNpc($npcsEntities[$npc['name']]);
			        $npcsLocationsTable[$place['name'] . '_' . $npc['name']]->setNumber($npc['number']);
			        $npcsLocationsTable[$place['name'] . '_' . $npc['name']]->setPlace($placesEntities[$place['name']]);
			
			        $entityManager->persist($npcsLocationsTable[$place['name'] . '_' . $npc['name']]);
		        }
	        }
        }
	
	    $charactersEntities = [];
        $charactersStatsTable = [];
        $charactersSpellsTable = [];
        $charactersInventoryTable = [];

        foreach ($characters as $key => $character) {
            $charactersEntities[$character['name']] = new Character();
            $charactersEntities[$character['name']]->setName($character['name']);
            // TODO: condition for checking description exists
            $charactersEntities[$character['name']]->setDescription($character['description']);
            $charactersEntities[$character['name']]->setLocation($placesEntities[$character['location']]);

            foreach ($character['stats'] as $stat) {
                $charactersStatsTable[$character['name'] . '_' . $stat['name']] = new CharacterStat();
                $charactersStatsTable[$character['name'] . '_' . $stat['name']]->setCharacter($charactersEntities[$character['name']]);
                $charactersStatsTable[$character['name'] . '_' . $stat['name']]->setStat($statsEntities[$stat['name'] . '_' . $stat['value']]);

                // $entityManager->persist($charactersStatsTable[$character['name'] . '_' . $stat['name']]);
            }

            foreach ($character['spells'] as $spell) {
                $charactersSpellsTable[$character['name'] . '_' . $spell['name']] = new CharacterSpell();
                $charactersSpellsTable[$character['name'] . '_' . $spell['name']]->setCharacter($charactersEntities[$character['name']]);
                $charactersSpellsTable[$character['name'] . '_' . $spell['name']]->setSpell($spellsEntities[$spell['name']]);

                // $entityManager->persist($charactersSpellsTable[$character['name'] . '_' . $spell['name']]);
            }

            if (isset($character['inventory'])) {
                foreach ($character['inventory'] as $item) {
                    $charactersInventoryTable[$character['name'] . '_' . $item['name']] = new CharacterInventory();
                    $charactersInventoryTable[$character['name'] . '_' . $item['name']]->setCharacter($charactersEntities[$character['name']]);
                    $charactersInventoryTable[$character['name'] . '_' . $item['name']]->setItem($itemsEntities[$item['name']]);

                     // $entityManager->persist($charactersInventoryTable[$character['name'] . '_' . $item['name']]);
                }
            }
        }

        // var_dump($charactersEntities);

        $entityManager->flush();

        return true;
    }

}

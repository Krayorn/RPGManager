<?php

namespace RPGManager\Utils;

use RPGManager\Template;

class Parser extends Template {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Parser();
        }
        return self::$instance;
    }

    public static function generateModelsDb($gameConfig, $settings) {
        $parser = Parser::getInstance();

        $line = "generateModelsDb() => Parsing models";
        $parser->writeAccessLog($line);

        $items = $gameConfig["items"];
        $monsters = $gameConfig["monsters"];
        $npcs = $gameConfig["npcs"];
        $characters = $gameConfig["characters"];
        $places = $gameConfig["places"];

        if (isset($settings["turnItemsOn"]) && $settings["turnItemsOn"]){
            $parser->parseItems($items);
        }

        return true;
    }

    private function parseItems($items) {
        
    }
}

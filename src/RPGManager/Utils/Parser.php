<?php

namespace RPGManager\Utils;

use RPGManager\Template;

class Parser extends Template
{
    private $config;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Parser();
        }
        return self::$instance;
    }

    public static function generateModelsDb($config)
    {
        $parser = Parser::getInstance();

        $line = "generateModelsDb() => Parsing models";
        $parser->writeAccessLog($line);

        return true;
    }
}

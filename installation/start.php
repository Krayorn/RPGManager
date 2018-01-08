<?php

require_once('vendor/autoload.php');
require_once('./config.php');

use RPGManager\Model\RegularMode;

$settingsGame = file_get_contents('config/schoolSettings.json');
$settings = json_decode($settingsGame, true);

RegularMode::startGame($entityManager, $settings);

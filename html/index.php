<?php
require __DIR__ . '/../vendor/autoload.php';
use Minix\Application;

$app = Application::getInstance();
$app->setEnvironment('local');
$app->run();

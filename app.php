<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new \Cilex\Application('Cilex');
$app->command(new \JuriyPanasevich\BJobs\Console\ListenCommand());
$app->command(new \JuriyPanasevich\BJobs\Console\WorkCommand());
$app->run();
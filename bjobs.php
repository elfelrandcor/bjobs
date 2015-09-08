<?php
require_once __DIR__.'/vendor/autoload.php';

$app = new \Cilex\Application('Cilex');
$app->command(new \JuriyPanasevich\BJobs\Console\BJobsCommand());
$app->run();
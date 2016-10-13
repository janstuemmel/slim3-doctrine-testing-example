<?php

require __DIR__ . '/vendor/autoload.php';

$app = new \Slim\App();

require __DIR__ . '/src/routes.php';

$container = $app->getContainer();

// configure doctrine orm
$ORMConfig = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration([ __DIR__ . '/src' ], true); // devmode true

$entityManager = Doctrine\ORM\EntityManager::create([
  'driver' => 'pdo_sqlite',
  'path' => __DIR__ . 'app.db'
], $ORMConfig);

// register to App
$container['orm'] = $entityManager;

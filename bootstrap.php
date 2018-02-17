<?php

require __DIR__ . '/vendor/autoload.php';

$app = new \Slim\App();

require __DIR__ . '/src/routes.php';

$config = require __DIR__ . '/config.php';

$container = $app->getContainer();

// configure doctrine orm
$ORMConfig = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration([ __DIR__ . '/src' ], true); // devmode true

$entityManager = Doctrine\ORM\EntityManager::create($config['database'], $ORMConfig);

// register to App
$container['orm'] = $entityManager;

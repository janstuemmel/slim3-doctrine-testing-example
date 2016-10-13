<?php

require __DIR__ . '/../vendor/autoload.php';

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Tools\SchemaTool;

use There4\Slim\Test\WebTestCase;

class TestCase extends WebTestCase {

  public $em;

  public function __construct() {

    parent::__construct();

    $ORMConfig = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/../src/Model' ], $debug = true);

    $this->em = EntityManager::create([ 'url' =>  'sqlite:///:memory:'], $ORMConfig);

    // create tables for the test
    $this->createTables();
  }

  public function getSlimInstance() {

    // include routes configuration
    require __DIR__ . '/../bootstrap.php';

    // overwrite database
    $container['orm'] = $this->em;

    return $app;
  }

  public function createTables() {
    $schemaTool = new SchemaTool($this->em);
    $classes = $this->em->getMetadataFactory()->getAllMetadata();
    $schemaTool->createSchema($classes);
  }
}

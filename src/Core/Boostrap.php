<?php

namespace App\Core;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class Boostrap
{
    const FILE_CONFIG_DATABASE = __DIR__ . '/../../config/database.json';

    private $dbParams;
    private $em;
    private $request;

    public function __construct()
    {        
        $this->dbParams = json_decode(file_get_contents(self::FILE_CONFIG_DATABASE), true);
    }

    public function init()
    {
        $this->createEntityManager();
        $this->initRequest();

        $dispatcher = new Dispatcher(new Router());
        $dispatcher->setEntityManager($this->em);
        $dispatcher->setRequest($this->request);
        $dispatcher->dispatch($this->request->getPathInfo());
    }

    public function createEntityManager()
    {
        $paths = array("/src/Entity");
        $isDevMode = false;
        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $config->setAutoGenerateProxyClasses(true);
        $this->em = EntityManager::create($this->dbParams, $config);
    }

    public function initRequest()
    {
        $this->request = Request::createFromGlobals();
    }
 }
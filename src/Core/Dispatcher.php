<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Dispatcher 
{
	const FILE_CONFIG = __DIR__ . '/../../config/config.json';
	const FILE_SERVICES = __DIR__ . '/../../config/services.json';
	const FILE_CREDENTIALS = __DIR__ . '/../../config/credentials.json';
	
	private $router;
    private $config;
	private $routing;
	private $services;
    private $em;
    private $request;
	private $servicesContainer;
	private $configsContainer;
	private $credentialsContainer;

    public function __construct(Router $router)
    {
		$this->router = $router;
		$this->config = json_decode(file_get_contents(self::FILE_CONFIG), true);
		$this->services = json_decode(file_get_contents(self::FILE_SERVICES), true);
		$this->credentials = json_decode(file_get_contents(self::FILE_CREDENTIALS), true);
		$this->initConfig();
		$this->initCredentials();
		$this->initServices();
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
		$this->router->setRequest($request);
    }

    public function dispatch($route)
    {
        try {
			if ($this->router->match($route)) {
				
				$className = $this->router->get('class');
				
				// Instancio el controller
				$allRequest = $this->request->query->all();
				$instance = new $className($allRequest);
				$instance->setEntityManager($this->em);
				
				if ($this->request !== null) {
					$instance->setRequest($this->request);
				}
				
				if (!empty($this->configsContainer)) {
					$instance->setConfig($this->configsContainer);
				}
				
				if (!empty($this->servicesContainer)) {
					$instance->setServices($this->servicesContainer);
				}
				
				if (!empty($this->credentialsContainer)) {
					$instance->setCredentials($this->credentialsContainer);
				}
				
				if (!empty($this->router->getRoutes())) {
					$instance->setRoutes($this->router->getRoutes());
				}
				
				$methodName = $this->router->get('method');
				$_params = $this->router->get('params');
				$params = ($_params != null) ? $_params : [];
				
				$response = call_user_func_array(array($instance, $methodName), array($this->request, $params));
				if ($response instanceof Response) {
					$view = new View($response);
					$view->render();
				} else if ($response instanceof View) {
					$response->renderViewData();
				} else if (is_string($response)) {
					echo $response;
				} else {
					throw new \Exception("View is invalid.");
				}
			} else {
				View::renderViewPageNotFound();
			}
            
        } catch (\Exception $e) {
            View::renderViewError($e->getMessage());
        }
    }
	
	public static function dispatchController($controllerName)
	{
		$controllerName = '\\App\\Controller\\' . $controllerName;
		
		$config = json_decode(file_get_contents(self::FILE_CONFIG), true);
		$credentials = json_decode(file_get_contents(self::FILE_CREDENTIALS), true);
		$dbParams = json_decode(file_get_contents(__DIR__ . '/../../config/database.json'), true);
		$services = json_decode(file_get_contents(self::FILE_SERVICES), true);
		$routing = json_decode(file_get_contents(__DIR__ . '/../../config/routing.json'), true);
		
		$paths = array("/src/Entity");
        $isDevMode = false;
        $configDb = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $configDb->setAutoGenerateProxyClasses(true);
        $em = \Doctrine\ORM\EntityManager::create($dbParams, $configDb);
		
		$configsContainer = [];
		$credentialsContainer = [];
		$servicesContainer = [];
		$routes = [];
		
		foreach($config as $key => $value) {
			$configsContainer[$key] = $value;
		}
				
		foreach($credentials as $key => $value) {
			$credentialsContainer[$key] = $value;
		}
		
		foreach($routing['routes'] as $item) {
			$routes[] = $item;
		}
		
		$parameters = [];
		$paramConfig = [];
		$paramCredentials;
		foreach($services['services'] as $item) {
			
			foreach($item['parameters'] as $param) {
				
				switch($param) {
					case '@config':
						$paramConfig = $configsContainer;
						break;	
						
					case '@credentials':
						$paramCredentials = $credentialsContainer;
						break;	
					
					default:
						$parameters = $param;
						break;
				}
			}
			
			$parameters = array_merge($parameters, $paramConfig, $paramCredentials);
			
			$className = $item['class'];
			$instance = new $className($parameters);
			
			$servicesContainer[$item['name']] = [
				'class' => $instance
			];
		}
		
		$instance = new $controllerName();
		$instance->setEntityManager($em);
		
		$request = Request::createFromGlobals();
		
		if ($request !== null) {
			$instance->setRequest($request);
		}
		
		if (!empty($routes)) {
			$instance->setRoutes($routes);
		}
		
		if (!empty($configsContainer)) {
			$instance->setConfig($configsContainer);
		}
		
		if (!empty($servicesContainer)) {
			$instance->setServices($servicesContainer);
		}
		
		if (!empty($credentialsContainer)) {
			$instance->setCredentials($credentialsContainer);
		}
		
		return $instance;
	}
	
	private function initServices()
	{
		$this->servicesContainer = [];
		$parameters = [];
		$paramConfig = [];
		$paramCredentials;
		foreach($this->services['services'] as $item) {
			
			foreach($item['parameters'] as $param) {
				
				switch($param) {
					case '@config':
						$paramConfig = $this->configsContainer;
						break;	
						
					case '@credentials':
						$paramCredentials = $this->credentialsContainer;
						break;	
					
					default:
						$parameters = $param;
						break;
				}
			}
			
			$parameters = array_merge($parameters, $paramConfig, $paramCredentials);
			
			$className = $item['class'];
			$instance = new $className($parameters);
			
			$this->servicesContainer[$item['name']] = [
				'class' => $instance
			];
		}
	}
	
	private function initConfig()
	{
		$this->configsContainer = [];
		foreach($this->config as $key => $value) {
			$this->configsContainer[$key] = $value;
		}
	}
	
	private function initCredentials()
	{
		$this->credentialsContainer = [];
		foreach($this->credentials as $key => $value) {
			$this->credentialsContainer[$key] = $value;
		}
	}
}
<?php

namespace App\Controller;

use App\Core\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Core\Dispatcher;

abstract class Controller 
{
	const FILE_CONFIG = __DIR__ . '/../../config/config.json';
	
	protected $parameters;
    protected $em;
	protected $request;
	protected $servicesContainer = [];
	protected $configsContainer = [];
	protected $credentialsContainer = [];
	protected $routersContainer = [];
	
	public function __construct(array $parameters = [])
	{
		$this->parameters = $parameters;
		$this->config = json_decode(file_get_contents(self::FILE_CONFIG), true);
	}
   
    public function setEntityManager($em)
    {
        $this->em = $em;
    }
	
	public function setRequest(Request $request) 
	{
		$this->request = $request;
	}
	
	public function setServices(array $services)
	{
		$this->servicesContainer = $services;
	}
	
	public function setConfig(array $config)
	{
		$this->configsContainer = $config;
	}
	
	public function setCredentials(array $credentials)
	{
		$this->credentialsContainer = $credentials;
	}
	
	public function setRoutes(array $routes) 
	{
		$this->routersContainer = $routes;
	}
	
	public function errorResponse(string $message) : JsonResponse
	{
		 $data = [
			'httpCode' => 500,
			'message' => $message
		];    

		return new JsonResponse($data);
	}
	
	public function successResponse(string $message) : JsonResponse
	{
		 $data = [
			'httpCode' => 200,
			'message' => $message
		];    

		return new JsonResponse($data);
	}
	
	public function getRequest() 
	{
		return $this->request;
	}
	
	protected function renderView(string $viewPath, array $parameters = []): View
    {
		$viewPath = 'src/' . $viewPath;
		if (is_file($viewPath)) {
			ob_start();
			extract($parameters);
			$baseUrl = $this->request->getBaseUrl();
			$config = $this->config;
			include $viewPath;
			$html = ob_get_clean();
			$view = new View();
			$view->setData($html);
			return $view;
			
		} else {
			die('No existe la vista');
		}
    }
	
	protected function get(string $name)
	{
		if (isset($this->servicesContainer[$name])) {
			return $this->servicesContainer[$name]['class'];
		}
		
		if (isset($this->configsContainer[$name])) {
			return $this->configsContainer[$name];
		}
		
		if (isset($this->credentialsContainer[$name])) {
			return $this->credentialsContainer[$name];
		}
	}
	
	protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }
	
	protected function redirectToRoute(string $routeName, array $parameters = [], int $status = 302): RedirectResponse
    {
        return $this->redirect($this->generateUrl($routeName, $parameters), $status);
    }
	
	protected function forward(string $controllerName, string $methodName, ...$parameters) 
	{
		$controllerInstance = Dispatcher::dispatchController($controllerName);
		return $controllerInstance->$methodName($parameters);
	}
	
	protected function generateUrl($routeName, array $parameters = [])
	{
		$routeFound = [];
		$url = '';
		foreach($this->routersContainer as $route) {
			
			if ($route['name'] == $routeName) {
				$routeFound = $route;
			}
		}
		
		if (!empty($parameters)) {
			$url = $this->request->getBaseUrl() . $routeFound['path'] . '?' . http_build_query($parameters);
			
		} else {
			$url = $this->request->getBaseUrl() . $routeFound['path'];
		}
		
		return $url;
	}
}
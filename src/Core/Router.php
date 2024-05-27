<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    const FILE_ROUTING = __DIR__ . '/../../config/routing.json';
	
	private $routing;
    private $request;
	private $route;
	private $routes;

    public function __construct()
    {
        $this->routing = json_decode(file_get_contents(self::FILE_ROUTING), true);
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function match($route)
    {
		$match = false;
        
		foreach($this->routing['routes'] as $item) {
			$this->routes[] = $item;
			if ($route == $item['path']) {
				if (!$this->request->isMethod($item['type'])) {
					throw new \Exception("Method is invalid.");
				}
				$match = true;
				$this->route = $item;
			}
		}
 
		return $match;
    }
	
	public function get($key)
	{
		if (isset($this->route[$key])) {
			return $this->route[$key];
		}
		
		return null;
	}
	
	public function getRoutes()
	{
		return $this->routes;
	}
}
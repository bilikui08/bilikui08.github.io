<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

class Url
{
	const FILE_CONFIG = __DIR__ . '/../../config/config.json';

	public static function generateUrl($path, $parameters = [])
	{
		$request = Request::createFromGlobals();
		
		$url = '';

		if (!empty($parameters)) {
			$url = self::getHttpOrigin() . $request->getBaseUrl() . $path . '?' . http_build_query($parameters);
		} else {
			$url = self::getHttpOrigin() . $request->getBaseUrl() . $path;
		}
		
		return $url;
	}
	
	public static function getHttpOrigin()
	{
		$config = json_decode(file_get_contents(self::FILE_CONFIG), true);
		$env =  $config['enviroment'];
		return $config['base_urls'][0][$env];
	}
}
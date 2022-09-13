<?php
namespace Repository\Component\Routing;

use Repository\Component\Http\Uri;
use Repository\Component\Support\Str;

/**
 * Route URL Generator.
 * 
 * @package	  \Repository\Component\Routing
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class UrlGenerator
{
	/**
	 * @var \Repository\Component\Routing\Route $route
	 */	
	private $route;

	/**
	 * @var \Psr\Http\Message\RequestInterface $request
	 */	
	private $request;
	
	/**
	 * @param \Repository\Component\Routing\Route $route
	 */	
	public function __construct(Route $route)
	{
		$this->route = $route;
	}
	
	/**
	 * Generate url from defined route name
	 * 
	 * @param string $alias
	 * @param spread|array $defaultValues
	 * 
	 * @return string|null
	 */	
	public function route(string $alias, ...$defaultValues)
	{
		$aliases = $this->route->getAliases();

		if (isset($aliases[$alias])) {
			$no = 0;
			$part = str_replace(array(
					'(?:http(?:s)?\://)?(?:www\.)?', 
					app_config('application.host')
				), 
				'', 
				$aliases[$alias]
			);

			$parts = explode('/', $this->decodeUrl($part));
			
			foreach ($parts as $key => $part) {
				if (Str::contains($part, ':')) {
					if (isset($defaultValues[$no])) {
						$parts[$key] = $defaultValues[$no];
					}
					
					$no++;
				}
			}

			$path = implode('/', $parts);

			$uri = new Uri(app_config('application.url'));
			$uri->withPath($path);

			return $uri->getUri();
		}
	}

	/**
	 * Decode url raw by the given encoded url
	 * 
	 * @param string $encodedUrl
	 * 
	 * @return string
	 */	
	public function decodeUrl(string $encodedUrl)
	{
		$parts = explode('/', $encodedUrl);
		return implode('/', array_map("rawurldecode", $parts));
	}

	/**
	 * Encode url raw by the given decoded url
	 * 
	 * @param string $decodedUrl
	 * 
	 * @return string
	 */
	public function encodeUrl(string $decodedUrl)
	{
		$parts = explode('/', $decodedUrl);
		return implode('/', array_map("rawurlencode", $parts));
	}
}

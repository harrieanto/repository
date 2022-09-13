<?php
namespace Repository\Component\Routing;

use Closure;
use RuntimeException;
use Repository\Component\Http\Uri;
use Psr\Http\Message\UriInterface;
use Repository\Component\Http\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Repository\Component\Collection\Collection;
use Repository\Component\Routing\Exception\RouteException;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Route Builder.
 * 
 * @package	  \Repository\Component\Routing
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Builder
{
	/**
	 * Container instance
	 *
	 * @var \Repository\Component\Contracts\ContainerInterface
	 */
	private $app;

	/**
	 * Middlewares routes container
	 *
	 * @var array
	 */
	public $middlewares = array();

	/**
	 * Regular routes container
	 * 
	 * @var array
	 */
	public $routes = array();

	/**
	 * Route groups
	 * 
	 * @var array $groups
	 */
	public $groups;

	/**
	 * Host group
	 * 
	 * @var string
	 */
	private $hostGroup;

	/**
	 * Path for each host group
	 * 
	 * @var string
	 */
	private $rootGroup;

	/**
	 * Route name/route aliases
	 * 
	 * @var array
	 */
	private $routeNames = array();

	/**
	 * Path/target name
	 * 
	 * @var string
	 */
	private $targetName;

	/**
	 * Custom regular expression for specific target route
	 * 
	 * @var array
	 */	
	private $routeExpressions = array();
	
	/**
	 * Default host name
	 * 
	 * @var string
	 */
	private $defaultHostName;

	/**
	 * Custom request method
	 *
	 * @var string $requestMethod
	 */
	protected $requestMethod = Request::GET;

	/**
	 * Uri Instance
	 * 
	 * @var \Psr\Http\Message\UriInterface
	 */
	protected $uri;

	/**
	 * Build custom request uri
	 * 
	 * @var string
	 */
	protected $requestUri;

	/**
	 * Request instance
	 * 
	 * @var \Psr\Http\Message\RequestInterface
	 */
	protected $request;
	
	/**
	 * Route files
	 * 
	 * @var array $routeFiles
	 */
	protected $routeFiles = array();
	
	/**
	 * 
	 * @param \Repository\Component\Contracts\ContainerInterface
	 * @param \Psr\Http\Message\UriInterface
	 * @param \Psr\Http\Message\RequestInterface
	 * @param \Psr\Http\Message\ResponseInterface
	 */
	public function __construct(
		ContainerInterface $app, 
		UriInterface $uri, 
		RequestInterface $request, 
		ResponseInterface $response)
	{
		$this->app = $app;
		$this->uri = $uri;
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * Set custom HTTP request method
	 * 
	 * @param string $httpMethod
	 * 
	 * @return \Repository\Component\Routing\Dispatcher
	 */
	public function setRequestMethod($htpMethod = Request::GET)
	{
		$this->requestMethod = $httpMethod;
		
		return $this;
	}

	/**
	 * Get current HTTP request method
	 * 
	 * @return string
	 */
	public function getRequestMethod()
	{
		$httpMethod = $this->request->getRequestMethod();

		if(empty($httpMethod)) $httpMethod = $this->requestMethod;
		
		return $httpMethod;
	}

	/**
	 * Build custom request
	 * 
	 * @param \Psr\Http\Message\UriInterface
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function setRequestUri(UriInterface $uri)
	{
		$this->requestUri = $uri->getUri();
		
		return $this;
	}

	/**
	 * Get custom request
	 * 
	 * @return string
	 */
	public function getRequestUri()
	{
		//Get default host from server parameters
		$host = $this->request->getHost();
		$scheme = $this->request->getHttpScheme();
		
		$this->uri->withScheme($scheme);
		$this->uri->withHost($host);
		$this->uri->withPort($this->request->getPort());
		
		//If exists, we just join it with current request uri
		$requestUri = $this->request->getCurrentUri();
		
		$requestUri = $this->uri->withPath($requestUri)->getUri();

		//Otherwise we could use custom request that has defined
		//through requestUri property
		if (empty($host)) $requestUri = $this->requestUri;
		
		//Don't miss to remove slash (/) character from request uri
		//to preventing target route misbehave
		return trim($requestUri, '/');
	}

	/**
	 * Get route configuration by the given grou
	 * 
	 * @return mixed
	 */
	public function getRouteParameter($group)
	{
		$routes = $this->app['config']['routes'][$group];
		
		return $routes;
	}

	/**
	 * Set name for spesific route name
	 * 
	 * @param string $key
	 * 
	 */
	public function setName($key)
	{
		$this->routeNames[$key] = $this->getTarget();
		
		return $this;
	}

	/**
	 * Get route by the given key
	 * 
	 * @param  string $key
	 * 
	 * @return string
	 */
	public function getName($key)
	{
		return $this->routeNames[$key];
	}

	/**
	 * Determine if the given route name has an alias
	 * 
	 * @param  string  $routeName
	 * 
	 * @return boolean
	 * 
	 */
	public function hasAlias($routeName)
	{
		if(Collection::make($this
			->getAliases())
			->contains($routeName)) return true;

		return false;
	}

	/**
	 * Get alias of the given route name
	 * 
	 * @param  string $routeName
	 * 
	 * @return string
	 */
	public function getAlias($routeName)
	{
		$routeAliases = Collection::make($this->routeNames);

		if($routeAliases->contains($routeName))
			return $routeAliases->flipForce()->get($routeName);
	}

	/**
	 * Get route aliases
	 * 
	 * @return array
	 */
	public function getAliases()
	{
		return $this->routeNames;
	}

	/**
	 * Handle route on the any HTTP request method
	 * 
	 * @param string $target Target path name
	 * @param \Closure|string $handler Spesific target route handler
	 * @param \Closure|string $middlewares Middleware handler
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function any($target, $handler, $middlewares = array())
	{		
		foreach (Request::$httpMethods as $httpMethod) {
			$this->resolveMiddleware($httpMethod, $target, $middlewares);

			$this->resolveRoute($httpMethod, $target, $handler);
		}
		
		return $this;
	}

	/**
	 * Handle route on the GET HTTP request method
	 * 
	 * @param string $target Target path name
	 * @param \Closure|string $handler Spesific target route handler
	 * @param \Closure|string $middlewares Middleware handler
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function get($target, $handler, $middlewares = array())
	{
		$httpMethod = Request::GET;
		
		$this->resolveMiddleware($httpMethod, $target, $middlewares);

		$this->resolveRoute($httpMethod, $target, $handler);
		
		return $this;
	}

	/**
	 * Handle route on the POST HTTP request method
	 * 
	 * @param string $target Target path name
	 * @param \Closure|string $handler Spesific target route handler
	 * @param \Closure|string $middlewares Middleware handler
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function post($target, $handler, $middlewares = array())
	{
		$httpMethod = Request::POST;
		
		$this->resolveMiddleware($httpMethod, $target, $middlewares);

		$this->resolveRoute($httpMethod, $target, $handler);
		
		return $this;
	}

	/**
	 * Handle route on the HEAD HTTP request method
	 * 
	 * @param string $target Target path name
	 * @param \Closure|string $handler Spesific target route handler
	 * @param \Closure|string $middlewares Middleware handler
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function head($target, $handler, $middlewares = array())
	{
		$httpMethod = Request::HEAD;
		
		$this->resolveMiddleware($httpMethod, $target, $middlewares);

		$this->resolveRoute($httpMethod, $target, $handler);
		
		return $this;
	}

	/**
	 * Handle route on the PUT HTTP request method
	 * 
	 * @param string $target Target path name
	 * @param \Closure|string $handler Spesific target route handler
	 * @param \Closure|string $middlewares Middleware handler
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function put($target, $handler, $middlewares = array())
	{
		$httpMethod = Request::PUT;
		
		$this->resolveMiddleware($httpMethod, $target, $middlewares);

		$this->resolveRoute($httpMethod, $target, $handler);
		
		return $this;
	}

	/**
	 * Handle route on the DELETE HTTP request method
	 * 
	 * @param string $target Target path name
	 * @param \Closure|string $handler Spesific target route handler
	 * @param \Closure|string $middlewares Middleware handler
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function delete($target, $handler, $middlewares = array())
	{
		$httpMethod = Request::DELETE;
		
		$this->resolveMiddleware($httpMethod, $target, $middlewares);

		$this->resolveRoute($httpMethod, $target, $handler);
		
		return $this;
	}

	/**
	 * Handle route on the OPTIONS HTTP request method
	 * 
	 * @param string $target Target path name
	 * @param \Closure|string $handler Spesific target route handler
	 * @param \Closure|string $middlewares Middleware handler
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function options($target, $handler, $middlewares = array())
	{
		$httpMethod = Request::OPTIONS;
		
		$this->resolveMiddleware($httpMethod, $target, $middlewares);

		$this->resolveRoute($httpMethod, $target, $handler);
		
		return $this;
	}

	/**
	 * Handle route on the PATCH HTTP request method
	 * 
	 * @param string $target Target path name
	 * @param \Closure|string $handler Spesific target route handler
	 * @param \Closure|string $middlewares Middleware handler
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function patch($target, $handler, $middlewares = array())
	{
		$httpMethod = Request::PATCH;
		
		$this->resolveMiddleware($httpMethod, $target, $middlewares);

		$this->resolveRoute($httpMethod, $target, $handler);
		
		return $this;
	}

	/**
	 * Handle route group
	 *
	 * @param  string|array $groups
	 * @param  \Closure $handler
	 *
	 * @return void
	 */
	public function group($groups, Closure $handler)
	{
		//The route group can be complicated
		//Usually occured when we take the deal
		//with different host and or sub host
		//Ex. mail.example.com, dashbaord.example.com
		//In the same level system
		//To achieve these all we could handle those requirement
		//in the array list with specific `host` and `root` path for the host itself
		if (is_array($groups)) {
			$this->setHostGroup($groups[Route::HOST_GROUP]);
			$this->setRootGroup($groups[Route::ROOT_GROUP]);
			$this->resolveBaseRootGroups($groups, $handler);
			
			return;
		}

		//Otherwise we just go ahead and bind the number of routes
		//in the same host system as group
		//If your current system running in the localhost:8080
		//Now, any target route in these group would be grouped as localhost:8080
		$this->setHostGroup($this->getDefaultHostname());
		$this->setRootGroup($groups);
		$this->resolveBaseRootGroup($groups, $handler);
	}

	/**
	 * Resolve base root group
	 *
	 * @param  string $group
	 * @param  \Closure $handler
	 *
	 * @return void
	 */
	private function resolveBaseRootGroup($group, Closure $handler)
	{
		$path = trim($group, '/');

		$collection = Collection::make(array());
		
		$group = trim($this->getHostGroup(), '/') . '/';
		
		$this->setHostGroup($group);

		$groups = array($group => array(Route::ROOT_GROUP => $path));

		$collection->add(Route::HOST_GROUP, $groups);

		$this->groups = $collection->all();

		call_user_func($handler, $this);

		//We should forget any group members
		//in the cache container `$groups`
		//So the group won't be exist
		//outside of the current target route handler 
		unset($this->groups);
	}
	
	/**
	 * Resolve base root groups
	 *
	 * @param  array $groups
	 * @param  \Closure $handler
	 *
	 * @return void
	 */
	private function resolveBaseRootGroups(array $groups, Closure $handler)
	{
		if (Collection::make($groups)->has(Route::HOST_GROUP)) {
			$group = $this->getHostGroup().'/';
			
			$this->setHostGroup($group);

			$collection = Collection::make(array());

			$rootGroup = $this->getRootGroup();
			$rootGroup = '/'.trim(
				$rootGroup, 
				'/'
			);

			$groups = array($group => array(
				Route::ROOT_GROUP => $rootGroup)
			);

			$collection->add(Route::HOST_GROUP, $groups);

			$this->groups = $collection->all();
			
			call_user_func($handler, $this);

			unset($this->groups);
		}
	}

	/**
	 * Determine if the current target route is in the group context
	 *
	 * @return bool
	 */	
	private function isInGroupContext()
	{
		return (isset($this->groups))?true:false;
	}

	/**
	 * Get current base route group
	 * Ex. example.com/v1, api.example.com/v1, etc...
	 *
	 * @return string
	 */	
	private function getBaseRouteGroup()
	{
		if($this->isInGroupContext()) {
			$host = $this->getHostGroup();
			$host = trim($host, '/');

			$rootGroup = $this->groups[Route::HOST_GROUP];
			$rootGroup = $rootGroup[$this->getHostGroup()][Route::ROOT_GROUP];
			
			$rootGroup = trim($rootGroup, '/');

			return $host . '/' . $rootGroup;
		}
	}

	/**
	 * Set current group host by the given host group name
	 *
	 * @param  string $hostName
	 *
	 * @return \Repository\Component\Routing\Builder
	 */	
	private function setHostGroup($hostName)
	{
		$this->hostGroup = $hostName;
		
		return $this;
	}

	/**
	 * Get current group host
	 *
	 * @return string
	 */
	private function getHostGroup()
	{
		return $this->hostGroup;
	}

	/**
	 * Set path root for current group host by the given path root name
	 *
	 * @param  string $pathName
	 *
	 * @return \Repository\Component\Routing\Builder
	 */	
	private function setRootGroup($pathName)
	{
		$this->rootGroup = $pathName;
		
		return $this;
	}

	/**
	 * Get path root for current group host
	 *
	 * @return string
	 */	
	private function getRootGroup()
	{
		return $this->rootGroup;
	}

	/**
	 * Get target route request
	 *
	 * @return string
	 */	
	public function getTarget()
	{
		return $this->targetName;
	}

	/**
	 * Resolve regular route request
	 * 
	 * @param string $httpMethod
	 * @param string $target
	 * @param \Closure|string $handler
	 * 
	 * @return void
	 */	
	public function resolveRoute($httpMethod, $target, $handler)
	{
		$this->setRoute($httpMethod, $target, $handler);
		
		return $this;
	}

	/**
	 * Build regular route request
	 * 
	 * @param string $httpMethod
	 * @param string $target
	 * @param \Closure|string $handler
	 * 
	 * @return void
	 */	
	public function setRoute($httpMethod, $target, $handler)
	{
		$target = trim($target, '/');

		if($this->isInGroupContext()) {
			$target = trim($this->getBaseRouteGroup(), '/') . '/' . $target;
			$target = trim($target, '/');
			$this->routes[$httpMethod][Route::TARGET_NAME][] = array($target => $handler);
		} else {
			$target = $this->getDefaultHostname() . '/' . $target;
			$target = trim($target, '/');
			$this->routes[$httpMethod][Route::TARGET_NAME][] = array($target => $handler);
		}

		$this->targetName = $target;
	}

	/**
	 * Resolve middleware route request
	 * 
	 * @param string $httpMethod
	 * @param string $target
	 * @param \Closure|string $handler
	 * 
	 * @return void
	 */	
	private function resolveMiddleware($httpMethod, $target, $middlewares)
	{
		if($middlewares || count($middlewares) > 0)
			$this->middleware($httpMethod, $target, $middlewares);
	}

	/**
	 * Build middleware route request(Similar to setMiddleware method)
	 * For convenient way
	 * 
	 * @param string $httpMethods
	 * @param string $target
	 * @param \Closure|string $handler
	 * 
	 * @return \Repository\Component\Routing\Builder
	 */
	public function middleware($httpMethods, $target, $handler)
	{
		$httpMethods = (array) $httpMethods;

		foreach ($httpMethods as $httpMethod) {
			$this->setMiddleware($httpMethod, $target, $handler);
		}
		
		return $this;
	}

	/**
	 * Build middleware route request
	 * 
	 * @param string $httpMethod
	 * @param string $target
	 * @param \Closure|string $handler
	 * 
	 * @return void
	 */	
	public function setMiddleware($httpMethod, $target, $handler)
	{
		$target = trim($target, '/');

		if($this->isInGroupContext()) {
			$target = $this->getBaseRouteGroup() . '/' . $target;
			$this->middlewares[$httpMethod][Route::TARGET_NAME][] = array($target => $handler);
		} else {
			$target = $this->getDefaultHostname() . '/' . $target;
			$this->middlewares[$httpMethod][Route::TARGET_NAME][] = array($target => $handler);
		}
	}

	/**
	 * Resolve any builded route request for dispatching
	 * by the given route category.
	 * Ex. Regulars routes and/or middlewares route
	 * 
	 * @param string $context Route category
	 * 
	 * @return void
	 */
	public function resolve($context = 'routes')
	{
		foreach($this->{$context} as $httpMethod => $routes) {
			$routes = $routes['target'];
			$httpMethods = explode('|', $httpMethod);

			foreach ($httpMethods as $httpMethod) {
				foreach ($routes as $key => $handlers) {
					foreach ($handlers as $url => $handler) {
						$this->{$context}[$httpMethod]['target'][$key] = array(
							$this->resolveRouteExpression($url) => $handler
						);
					}
				}
			}
		}
	}

	/**
	 * Get default host name from configuration
	 * 
	 * @return string
	 */
	public function getDefaultHostname()
	{
		$host = $this->getRouteParameter('host');

		if (array_key_exists('host', $parts = parse_url($host))) {
			$host = $parts['host'];
		}

		return  '(?:http(?:s)?\://)?(?:www\.)?' .  $host;
	}

	/**
	 * Set default host name if the configuration value is misssing
	 * 
	 * @return \Repository\Component\Routing\Route
	 */
	public function setDefaultHostname($host)
	{
		$this->defaultHostName = $host;
		
		return $this;
	}

	/**
	 * Get route groups list
	 * 
	 * @return array
	 */
	public function getGroups()
	{
		return $this->groups;
	}

	/**
	 * Get regular routes
	 * 
	 * @return array
	 */
	public function getRoutes()
	{
		return $this->routes;
	}

	/**
	 * Get middleware routes
	 * 
	 * @return array
	 */
	public function getMiddlewareRoutes()
	{
		return $this->middlewares;
	}

	/**
	 * Add prefix to the given target rule
	 *
	 * @param  string $key
	 * @param  string $prefix
	 *
	 * @return string
	 */
	public function addPrefix($target, string $prefix = ':')
	{
		$expression = $prefix.$target;
		
		return $expression;
	}

	/**
	 * Resolve route path by specific pattern
	 * 
	 * @param array $target Route path name
	 *
	 * @return \Repository\Component\Routing\Route
	 */
	public function resolveRouteExpression(&$target)
	{
		$this->numericCase();
		$this->alphaCase();
		$this->alnumCase();
		$this->anyCase();
		$this->lowerCase();
		$this->upperCase();

		$patterns = $this->getRouteExps();
		
		$targets = explode('/', $target);

		foreach($patterns as $type => $pattern) {
			if(in_array($type, $targets)) {

				foreach($targets as $index => $target) {
					if($target === $type) {
						$target = str_replace($target, $pattern, $target);
						$targets[$index] = $target;
					}
				}
			}
		}
		
		$target = implode('/', $targets);
		
		return $target;
	}

	/**
	 *
	 * For convenient way to set paired rule path to the route
	 * 
	 * @param string $key
	 * @param string $value pattern
	 *
	 * @return  void
	 * 
	 */	
	public function expression($key, $value)
	{
		$this->setRouteExp($key, $value);
		return $this;
	}

	/**
	 * 
	 * Set paired rule path to the route
	 * 
	 * @param string $key
	 * @param string $value pattern
	 *
	 * @return  void
	 * 
	 */
	public function setRouteExp($key, $value)
	{
		$this->routeExpressions[$key] = $value;
	}

	public function getRouteExp($key)
	{
		return $this->routeExpressions[$key];
	}

	/**
	 *
	 * Get another path rules
	 * 
	 * @return array
	 * 
	 */
	public function getRouteExps()
	{
		return $this->routeExpressions;
	}

	/**
	 * Handle only numeric target route allowed
	 * 
	 * @return \Repository\Component\Routing\Route
	 */
	public function numericCase()
	{
		$type = $this->addPrefix(Route::NUMERIC_TYPE);
		//match with digit
		$this->setRouteExp($type, '([0-9]+)+');
		
		return $this;
	}

	/**
	 * Handle only alphabetical target route allowed
	 * 
	 * @return \Repository\Component\Routing\Route
	 */
	public function alphaCase()
	{
		//just match with alpha character
		$type = $this->addPrefix(Route::ALPHA_TYPE);

		$this->setRouteExp($type, '([A-Za-z\_\-]+)+');
		
		return $this;
	}

	/**
	 * Handle numeric and alphabetical target route allowed
	 * 
	 * @return \Repository\Component\Routing\Route
	 */
	public function alnumCase()
	{
		//match with both of alpha and or numeric characters
		$type = $this->addPrefix(Route::ALNUM_TYPE);
		
		$this->setRouteExp($type, '([0-9A-Za-z\_\-]+)');

		return $this;
	}

	/**
	 * Handle only alphabetical upper case target route allowed
	 * 
	 * @return \Repository\Component\Routing\Route
	 */
	public function upperCase()
	{
		//only match with uppercase characters
		$type = $this->addPrefix(Route::UPPER_TYPE);
		
		$this->setRouteExp($type, '([A-Z]+)+');

		return $this;
	}

	/**
	 * Handle only alphabetical lower case target route allowed
	 * 
	 * @return \Repository\Component\Routing\Route
	 */
	public function lowerCase()
	{
		//only match with lowercase characters
		$type = $this->addPrefix(Route::LOWER_TYPE);
		
		$this->setRouteExp($type, '([a-z]+)+');

		return $this;
	}

	/**
	 * Handle any character of target route
	 * 
	 * @return \Repository\Component\Routing\Route
	 */
	public function anyCase()
	{
		//match with any requested characters
		$type = $this->addPrefix(Route::ANY_TYPE);
		
		$this->setRouteExp($type, '([^/].+)+');

		return $this;
	}

	/**
	 * Get entire route details
	 * 
	 * @return array
	 */
	public function getRouteDetails()
	{
		$items = Collection::make(array());
		$items->add('routes', $this->getRoutes());
		$items->add('middlewares', $this->getMiddlewareRoutes());
		$items->add('aliases', $this->getAliases());

		return $items;
	}

	/**
	 * Resolve registered route
	 * 
	 * @return void
	 *
	 * @throws  \RouteException
	 */
	public function resolveRegisteredRoute()
	{
		$target = Collection::make($this->getPaths());

		if ($target->count() > 0) {
			$target->map(function($targets) {
				foreach ($targets as $directory => $paths) {
					$realpath = Collection::make($paths);

					$realpath->map(function ($path) use ($directory) {
						if (!$this->app['fs']->isDirectory($directory)) {
							throw new RouteException("Directory [$directory] not found");
						}
										
						$target = $directory . '/' . $path;

						if (!$this->app['fs']->isFile($target)) {
							throw new RouteException("File [$target] not found");
						}
						
						require $target;
					});
				}
			});
		}
	}

	/**
	 * Set route files
	 * 
	 * @param string $dirName
	 * @param string $fileName
	 *
	 * @return  \Repository\Component\Routing\Builder
	 */
	public function addPath($dirName, $fileName)
	{
		$this->routeFiles[][$dirName][] = trim($fileName, '/');
		
		return $this;
	}

	/**
	 * Get route files
	 * 
	 * @return array Route files
	 */
	public function getPaths()
	{
		return (array) $this->routeFiles;
	}

	/**
	 * Cache available routes
	 * 
	 * @return void
	 */
	public function cacheRoutes()
	{
		$path = $this->app->getCachedRoutesPath();

        $routes = "<?php\n\nreturn " .var_export(array(
        	'routes' => $this->getRoutes(), 
        	'middlewares' => $this->getMiddlewareRoutes(), 
        	'aliases' => $this->getAliases()
        ), true) .";\n";

		$handle = fopen($path, 'w+');

		fwrite($handle, $routes);
		fclose($handle);
	}

	/**
	 * Extract Cached Routes
	 * 
	 * @return mixed
	 */
	public function extractCachedRoutes()
	{

		$path = $this->app->getCachedRoutesPath();

		if (!$this->app->routesAreCached()) {
			return;
		}

		extract(require $this->app->getCachedRoutesPath());

		$this->routes = $routes;
		$this->middlewares = $middlewares;
		$this->routeNames = $aliases;
	}
}
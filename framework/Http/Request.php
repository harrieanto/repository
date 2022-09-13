<?php
namespace Repository\Component\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Repository\Component\Support\Str;
use Repository\Component\Collection\Collection;

/**
 * Extend PSR-7 In-Bound Request.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Request extends ServerRequest
{
	/**
	 * The list of trusted proxy Ips
	 * @var array $trustedProxies
	 */
	private static $trustedProxies = [];

	/**
	 * The list of trusted headers
	 * @var array $trustedHeaderNames
	 */
	private static $trustedHeaderNames = [
		'forwarded' => 'Forwarded',
		'client-ip' => 'X-Forwarded-For',
		'client-host' => 'X-Forwarded-Host',
		'client-port' => 'X-Forwarded-Port',
		'client-proto' => 'X-Forwarded-Proto'
	];

	/**
	 * HTTP Method lists
	 * @var array
	 */
	public static $httpMethods = array(
		'get', 
		'put', 
		'post', 
		'delete', 
		'patch', 
		'head', 
		'options'
	);

	/**
	 * HTTP Standard ports
	 * @var array
	 */
	public static $standardPorts = array(
		'ftp' => 21, 
		'tls' => 25, 
		'ssh' => 22, 
		'http'=> 80, 
		'https'=> 443
	);

	/**
	 * Previous uri
	 * @var string
	 */
	private $previousUrl;

	 /** The Header Host **/
	const HEADER_HOST = 'Host';

	 /** The Header Content Type **/
	const HEADER_CONTENT_TYPE = 'Content-Type';

	 /** The Header Content Length **/
	const HEADER_CONTENT_LENGTH = 'Content-Length';

	 /** The Http GET Method **/
	const GET = 'get';

	 /** The Http POST Method **/
	const POST = 'post';

	 /** The Http PUT Method **/
	const PUT = 'put';

	 /** The Http DELETE Method **/
	const DELETE = 'delete';

	 /** The Http OPTIONS Method **/
	const OPTIONS = 'options';

	 /** The Http PATCH Method **/
	const PATCH = 'patch';

	 /** The Http HEAD Method **/
	const HEAD = 'head';

	 /** The Header Encoded Form **/
	const FORM_ENCODED =  'application/x-www-form-urlencoded';

	 /** The Header Multiple Form **/
	const MULTI_FORM = 'multipart/form-data';

	 /** The default body stream used for write and read stream **/
	const DEFAULT_BODY_STREAM = 'php://input';

	 /** The default request target path **/
	const DEFAULT_REQUEST_TARGET = '/';

	const CLIENT_PORT = 'client-port';

	const CLIENT_PROTO = 'client-proto';
	
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		$target = null, 
		$method = null, 
		StreamInterface $body = null, 
		$headers = null, 
		$version = null)
	{
		//Define request
		parent::__construct($target, $method, $body, $headers, $version);
	}

	/**
	 * Sets a trusted header name
	 *
	 * @param string $name The name of the header
	 * @param mixed $value The value of the header
	 * 
	 * @return void
	 */
	public static function setTrustedHeaderName(string $name, $value)
	{
		self::$trustedHeaderNames[$name] = $value;
	}

	/**
	 * Sets the list of trusted proxy Ips
	 *
	 * @param array|string $trustedProxies The list of trusted proxies
	 * 
	 * @return void
	 */
	public static function setTrustedProxies($trustedProxies)
	{
		self::$trustedProxies = (array)$trustedProxies;
	}

	/**
	 * Gets whether or not we're using a trusted proxy
	 *
	 * @return bool True if using a trusted proxy, otherwise false
	 */
	private function isUsingTrustedProxy() : bool
	{
		if ($this->isRunningInConsole()) { return false; }

		$remoteAddress = $this->getServerParams()['REMOTE_ADDR'];

		return in_array($remoteAddress, self::$trustedProxies);
	}

	/**
	 * Get http server Host
	 * 
	 * @throw \InvalidArgumentException When host contains invalid character
	 * 
	 * @return string
	 */
	public function getHost()
	{
		$host = null;

		if ($this->isUsingTrustedProxy() && $this->hasHeader(self::$trustedHeaderNames['client-host'])) {
			$hosts = explode(',', $this->getHeaderLine(self::$trustedHeaderNames['client-host']));
			$host = trim(end($hosts));
		}

		if (!$host) {
			$host = $this->getHeaderLine('HOST');
		}

		if (!$host) {
			$host = $this->getServerParam('SERVER_NAME');
		}

		if (!$host) {
			$host = $this->getServerParam('SERVER_ADDR');
		}

		// Remove the port number
		$host = strtolower(preg_replace("/:\d+$/", '', trim($host)));
		
		// Check for forbidden characters
		// Credit: Symfony HTTPFoundation
		if (!empty($host) && !empty(preg_replace("/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/", '', $host))) {
			throw new InvalidArgumentException("Invalid host \"$host\"");
		}

		return $host;
	}

	public function getFullUrl()
	{
		$uri = new Uri;
		$uri->withScheme($this->getHttpScheme());
		$uri->withHost($this->getHost());
		$uri->withPort($this->getPort());
		$uri->withPath($this->getRequestUri());
		$uri->withQuery($this->getQueryParams());

		return $uri->getUri();
	}
	
	public function getFullHost()
	{
		$uri = new Uri;
		$uri->withScheme($this->getHttpScheme());
		$uri->withHost($this->getHost());
		$uri->withPort($this->getPort());

		return $uri->getUri();
	}

	/**
	 * Get http server port
	 * 
	 * @return string
	 */
	public function getPort()
	{
		if ($this->isUsingTrustedProxy()) {
			if ($this->hasHeader(self::$trustedHeaderNames[self::CLIENT_PORT])) {
                return (int) $this->getHeaderLine(self::$trustedHeaderNames[self::CLIENT_PORT]);
            } else if ($this->getHeaderLine(self::$trustedHeaderNames[self::CLIENT_PROTO]) === 'https') {
				return 443;
			}
		}

		if ($this->hasServerParam('SERVER_PORT')) {
			return (int) $this->getServerParam('SERVER_PORT');
		}

		return (int) self::$standardPorts[$this->getHttpScheme()];
	}

	/**
	 * Get HTTP referer
	 * 
	 * @return string|null
	 */
	public function getPreviousUri(bool $fallbackToReferer = false)
	{
		if (!empty($this->previousUrl)) {
			return $this->previousUrl;
		}

		if ($fallbackToReferer) {
			$request = $this->getHeaderLine('REFERER');
			return !isset($request) ?
				self::DEFAULT_REQUEST_TARGET : $request;
		}
	}

	/**
	 * Get HTTP referer
	 * 
	 * @return HttpRequest
	 */
	public function setPreviousUri(UriInterface $url)
	{
		$this->previousUrl = $url;
		return $this;
	}

	/**
	 * Get the input source for the request.
	 *
	 * @return json|array
	 */
	public function getInputSource()
	{
		$parsedBody = $this->getParsedBody();
		
		if ($this->getRequestMethod() === Request::GET) {
			return $this->getQueryParams();
		}
		
		return $parsedBody;
	}

	public function has($field)
	{
		$sources = (array) $this->getInputSource();

		if (array_key_exists($field, $sources)) {
			return true;
		}

		return false;
	}
	
	/**
	 * Get query param by the given key
	 * 
	 * @param string $field

	 * @return array|null
	 */	
	public function get($field)
	{
		$parsedBody = (array) $this->getParsedBody();
		
		if ($this->getRequestMethod() === Request::GET && isset($this->getQueryParams()[$field])) {
			return $this->getQueryParams()[$field];
		}

		if (isset($parsedBody[$field])) {
			return $parsedBody[$field];
		}
	}

	/**
	 * Get current request uri
	 * 
	 * @return string
	 */
	public function getCurrentUri()
	{
		// Current Request URI
		if (!isset($this->getServerParams()['REQUEST_URI'])) {
			return '';
		}

		$uri = $this->getServerParams()['REQUEST_URI'];

		// Don't take query params into account on the URL
		if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));

		// Remove trailing slash + enforce a slash at the start
		$uri = '/' . trim($uri, '/');

		return $uri;
	}


	/**
	 * Get last uri or directory based on last optional sign
	 * 
	 * @param  string $path uri|directory
	 * @param  string $separator sign conditional that will use to parse last rel
	 * 
	 * @return string
	 */
	public function getLastPath(string $path,  $separator = "/")
	{
		$path = trim($path, '/');
		//bind to string current url or even directory
		$lastPath = implode($separator, array_slice(
			explode($separator, $path), 0, -1
			)
		);

		//fetch last path
		$lastPath = substr($path, strlen($lastPath));

		//when url contain ask sign then separate again it
		if (strstr($lastPath,  '?')) {
			$lastPath = substr($lastPath, 0, strpos($lastPath, '?'));
		}

		return $separator . trim($lastPath, $separator);
	}

	/**
	 * Get path by last offset
	 * 
	 * @param string $path

	 * @return string
	 */
	public function getPathByLastOffset($path)
	{
	   $path = trim($path, '/');

	   $offset = $this->getLastPath($path);
	   
	   $path = substr($path, 0, -strlen($offset));
	   
	   return $path;
	}

	/**
	 * Turn a header string into an array.
	 * 
	 * @param string $header
	 * 
	 * @return array
	 */
	protected function headerToArray($header)
	{
		$headers = [];
		$parts = explode("\r\n", $header);
		foreach ($parts as $singleHeader) {
			$delimiter = strpos($singleHeader, ': ');
			if ($delimiter !== false) {
				$key = substr($singleHeader,  0,  $delimiter);
				$val = substr($singleHeader,  $delimiter + 2);
				$headers[$key] = $val;
			}
			else {
				$delimiter = strpos($singleHeader, ' ');
				if ($delimiter !== false) {
					$key = substr($singleHeader,  0,  $delimiter);
					$val = substr($singleHeader,  $delimiter + 1);
					$headers[$key] = $val;
				}
			}
		}
		return $headers;
	}

	/**
	 * Set method to the current request
	 * 
	 * @param string $methodRequest {POST, PUT, DELETE, PATCH, GET, OPTIONS, HEAD}
	 * 
	 * @return void
	 */
	public function setRequestMethod(string $methodRequest)
	{		
		if (!in_array($methodRequest, self::$httpMethods)) {
			$this->withServerParams(array('REQUEST_METHOD' => ''));
		} else {
			$this->withServerParams(array('REQUEST_METHOD' => mb_strtoupper($methodRequest)));
		}
	}

	/**
	 * 
	 * Get method of the request header
	 * 
	 * @return string
	 * 
	 */
	public function getMethod()
	{
		//current method
		$httpMethod = mb_strtoupper($this->getRequestMethod());
		// If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
		// @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
		if ($httpMethod === Request::HEAD) {
			$httpMethod = Request::GET;
		}

		//When request is POST then check whether method override was sent and check whether in the method override have an annother method
		//like PUT, PATCH or DELETE 
		if ($httpMethod === Request::POST) {
			$httpHeaders = $this->getServerParams();

			if (isset($httpHeaders['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
				$overrideMethod = $httpHeaders['HTTP_X_HTTP_METHOD_OVERRIDE'];

				if (in_array($overrideMethod, array('PUT', 'DELETE', 'PATCH'))) {
					$httpMethod = $overrideMethod;
				}
			} else {
				$httpMethod = $httpHeaders['REQUEST_METHOD'];
			}
		}

		//return current method
		return $httpMethod;
	}

	/**
	 * Get HTTP request URI
	 * 
	 * @return string
	 */
	public function getRequestUri()
	{
		if (isset($this->getServerParams()['REQUEST_URI'])) {
			return $this->getServerParams()['REQUEST_URI'];
		}
	}

	/**
	 * Get server param by the given key
	 * 
	 * @return string|null
	 */
	public function getServerParam(string $key)
	{
		if (isset($this->getServerParams()[$key])) {
			return $this->getServerParams()[$key];
		}

		return null;
	}

	/**
	 * Determine if server params contains value by the given key
	 * 
	 * @return bool
	 */
	public function hasServerParam(string $key)
	{
		return isset($this->getServerParams()[$key]) ? true : false;
	}

	/**
	 * Check if the request has been made through
	 * xml http request
	 * 
	 * @return bool true
	 * Whenever HTTP_XREQUESTED_WITH not empty and false otherwise
	 */
	public function isAjax()
	{
		$ajax = $this->getHeaderLine('X-Requested-With');
		return (!empty($ajax) && $ajax === 'XMLHttpRequest' )?true:false;
	}

	/**
	 * Check if the request has been made through
	 * plain text http request
	 * 
	 * @return bool true
	 * Whenever HTTP_XREQUESTED_WITH not empty and false otherwise
	 */
	public function isPlain()
	{
		return Str::contains($this->getHeaderLine('Content-Type'), '/plain');
	}

	/**
	 * Check if the request has been made through
	 * xml http request
	 * 
	 * @return bool true
	 * Whenever HTTP_XREQUESTED_WITH not empty and false otherwise
	 */
	public function isXml()
	{
		return Str::contains($this->getHeaderLine('Content-Type'), '/xml');
	}

	/**
	 * Determine if the request is JSON.
	 *
	 * @return bool
	 */
	public function isJson()
	{
		return Str::contains($this->getHeaderLine('Content-Type'), '/json');
	}

	/**
	 * Determine if the request is over HTTPS.
	 *
	 * @return bool
	 */
	public function isSecure() : bool
	{
		if ($this->isUsingTrustedProxy() && $protoString = $this->getHeaderLine(self::$trustedHeaderNames[self::CLIENT_PROTO])) {
			$protoArray = explode(',', $protoString);

			return count($protoArray) > 0 && in_array(strtolower($protoArray[0]), ['https', 'ssl', 'on']);
		}
		
		$https = 'off';
		
		if (isset($this->getServerParams()['HTTPS'])) {
			$https = $this->getServerParams()['HTTPS'];
		}
		
		return $https !== 'off';
	}

	/**
	 * Get http scheme
	 * 
	 * @return string
	 */
	public function getHttpScheme()
	{
		$scheme = 'http';
		
		if ($this->isSecure()) {
			$scheme = 'https';
		}
		
		return $scheme;
	}

	/**
	 * Determine if application is running over cli server
	 * 
	 * @return boolean
	 */
	public function isRunningInConsole()
	{
		return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
	}
}
<?php
namespace Repository\Component\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * PSR-7 HTTP Response.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Response extends Message implements ResponseInterface
{
	/** The default http version **/
	const VERSION = '1.1';

	/** The default status code response **/
	const DEFAULT_STATUS_CODE = 200;

	/** The mime type of json **/
	const CONTENT_TYPE_JSON = "application/json";

	/** The mime type of hal+json **/
	const CONTENT_TYPE_HAL_JSON = "application/hal+json";

	/**
	 * HTTP status codes
	 * @var array $statusCodes
	 */
	public static $statusCodes = array(
		100 => 'Continue',  
		101 => 'Switching Protocols', 
		200 => 'OK', 
		201 => 'Created', 
		202 => 'Accepted',  
		203 => 'Non-Authoritative Information', 
		204 => 'No Content', 
		205 => 'Reset Content',  
		206 => 'Partial Content', 
		300 => 'Multiple Choices', 
		301 => 'Moved Permanently', 
		302 => 'Moved Temporarily',  
		303 => 'See Other', 
		304 => 'Not Modified', 
		305 => 'Use Proxy', 
		400 => 'Bad Request', 
		401 => 'Unauthorized',  
		402 => 'Payment Required', 
		403 => 'Forbidden', 
		404 => 'Not Found', 
		405 => 'Method Not Allowed',  
		406 => 'Not Acceptable', 
		407 => 'Proxy Authentication Required', 
		408 => 'Request Time-out',  
		409 => 'Conflict', 
		410 => 'Gone', 
		411 => 'Length Required', 
		412 => 'Precondition Failed',  
		413 => 'Request Entity Too Large', 
		414 => 'Request-URI Too Large', 
		415 => 'Unsupported Media Type',  
		500 => 'Internal Server Error', 
		501 => 'Not Implemented', 
		502 => 'Bad Gateway', 
		503 => 'Service Unavailable',  
		504 => 'Gateway Time-out', 
		505 => 'HTTP Version not supported', 
	);

	/**
	 * Cookies container
	 * @var array $cookies
	 */
	protected $cookies;

	/**
	 * @param int $statuscode
	 * @param \Psr\Http\Message\StreamInterface $body The body response
	 * @param array $headers Header response list
	 * @param float $version The http protocol version
	 */
	public function __construct(
		$statusCode = null,
		StreamInterface $body = null,
		$headers = array(),
		$version = null)
	{
		$this->body = $body;
		$this->status['code'] = $statusCode ?? self::DEFAULT_STATUS_CODE;
		$this->status['reason'] = self::$statusCodes[$statusCode] ?? '';
		$this->httpHeaders = $headers;
		$this->version = $this->onlyVersion($version) ?? self::VERSION;
		
		if ($statusCode) $this->setStatusCode();
	}

	/**
	 * Determine if the given cookie name has an cookie value
	 *
	 * @param string $cookieName
	 * 
	 * @return bool
	 */
	public function hasCookie($cookieName)
	{
		if (isset($_COOKIE[$cookieName])) return true;
		
		return false;
	}

	/**
	 * Get cookie value from the given key/cookie name
	 *
	 * @param string $cookieName
	 * 
	 * @return mixed
	 */
	public function getCookie($cookieName)
	{
		if ($this->hasCookie($cookieName))
			return $_COOKIE[$cookieName];
	}

	/**
	 * Delete cookie in the response header
	 *
	 * @param string $name The name of the cookie to delete
	 * @param string $path The path the cookie is valid on
	 * @param string $domain The domain the cookie is valid on
	 * @param bool $isSecure Whether or not the cookie was secure
	 * @param bool $isHttpOnly Whether or not the cookie was HTTP-only
	 * 
	 * @return void
	 */
	public function deleteCookie(
		string $name,
		string $path = '/',
		string $domain = '',
		bool $isSecure = false,
		bool $isHttpOnly = true)
	{
		// Remove the cookie from the response
		$this->setCookie(new Cookie($name, '', 0, $path, $domain, $isSecure, $isHttpOnly));
	}
	
	/**
	 * Send header given by headers list
	 * 
	 * @param Boolean $cookie Whether or not sending cookie alongside headers
	 * 
	 * @return void
	 */
	public function sendHeaders(bool $cookie = true)
	{
		if (isset($this->httpHeaders['Host'])) {
			unset($this->httpHeaders['Host']);
		}
		
		if (!headers_sent()) {
			header(
				sprintf(
					'HTTP/%s %s %s',
					$this->getProtocolVersion(),
					$this->getStatusCode(),
					$this->getReasonPhrase()
				),
				true,
				$this->getStatusCode()
			);
			
			if ($cookie) {
				// Send the cookies
				foreach ((array) $this->getCookies(true) as $cookie) {
					$this->sendCookie($cookie);
				}
			}
			
			foreach ((array) $this->httpHeaders as $name => $value) {
				header($name . ':' . $value, strcasecmp($name, 'Content-Type') === 0, $this->getStatusCode());
			}
		}

		if (extension_loaded('zlib')) {
			if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
				ob_end_flush();
			}
		} else {
			ob_flush();
		}
	}

	/**
	 * Send cookie
	 *
	 * @param Cookie $cookie The cookie parameter
	 * 
	 * @return null
	 */
	private function sendCookie(Cookie $cookie)
	{
		$path = $cookie->getPath();
		$samesite = $cookie->getSameSite();

		if (PHP_VERSION_ID < 70300) {
			$path = $path . '; samesite=' . $samesite;

			setcookie(
				$cookie->getName(), 
				$cookie->getValue(), 
				$cookie->getExpiration(), 
				$path, 
				$cookie->getDomain(), 
				$cookie->isSecure(), 
				$cookie->isHttpOnly()
			);

			return;
		}

		$options = array(
			'expires' => $cookie->getExpiration(), 
			'path' => $path, 
			'domain' => $cookie->getDomain(), 
			'samesite' => $samesite, 
			'secure' => $cookie->isSecure(), 
			'httponly' => $cookie->isHttpOnly()
		);

		setcookie($cookie->getName(), $cookie->getValue(), $options);
	}

	/**
	 * Get a list of all the active cookies
	 *
	 * @param bool $includeDeletedCookies Whether or not to include deleted cookies
	 * 
	 * @return array \Repository\Component\Http\Cookie[] The list of all the set cookies
	 */
	public function getCookies(bool $includeDeletedCookies = false) : array
	{
		$cookies = [];

		foreach ((array) $this->cookies as $domain => $cookiesByDomain) {
			foreach ($cookiesByDomain as $path => $cookiesByPath) {
				/**
				 * @var string $name
				 * @var Cookie $cookie
				 */
				foreach ($cookiesByPath as $name => $cookie) {
					// Only include active cookies
					if ($includeDeletedCookies || $cookie->getExpiration() >= time()) {
						$cookies[] = $cookie;
					}
				}
			}
		}

		return $cookies;
	}

	/**
	 * Sets a cookie
	 *
	 * @param Cookie $cookie The cookie to set
	 * 
	 * @return void
	 */
	public function setCookie(Cookie $cookie)
	{
		$this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()] = $cookie;
	}

	/**
	 * Set multiple cookies
	 *
	 * @param array $cookies
	 * 
	 * @return void
	 */
	public function setCookies(array $cookies)
	{
		foreach ($cookies as $cookie) {
			$this->setCookie($cookie);
		}
	}

	/**
	 * Set the response status code to the http response header.
	 *
	 * @return void
	 */
	public function setStatusCode()
	{
		http_response_code($this->getStatusCode());
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getStatusCode()
	 */
	public function getStatusCode()
	{
		return $this->status['code'];
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::withStatus()
	 */
	public function withStatus($statusCode, $reasonPhrase = '')
	{
		if (!isset(self::$statusCodes[$statusCode])) {
			throw new InvalidArgumentException(
				"Invalid Status Code ". $statusCode
			);
		}
		
		$this->status['code'] = $statusCode;
		$this->status['reason'] = ($reasonPhrase)?
			self::$statusCodes[$statusCode] : 
			null;

		$this->setStatusCode();

		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message::getReasonPhrase()
	 */
	public function getReasonPhrase()
	{
		return $this->status['reason']??
			self::$statusCodes[$this->status['code']] ??
			'';
	}
	
	/**
	 * @inheritdoc
	 * See \Repository\Component\Http\DownloadResponse::__construct()
	 */
	public function download(string $filePath, string $fileName = null, $disposition = 'attachment')
	{
		$response = new DownloadResponse($filePath, $fileName, $disposition);

		$this->withBody($response->getBody());
		$this->httpHeaders = array_merge(
			$this->getHttpHeaders(), 
			$response->getHttpHeaders()
		);
		$this->withStatus(
			$response->getStatusCode(), 
			$response->getReasonPhrase()
		);

		return $this;
	}

	/**
	 * @inheritdoc
	 * See \Repository\Component\Http\DownloadResponse::__construct()
	 */
	public function json($content = [], int $statusCode = 200, array $headers = [])
	{
		$response = new JsonResponse($content, $statusCode, $headers);

		$this->withBody($response->getBody());
		$this->httpHeaders = array_merge(
			$this->getHttpHeaders(), 
			$response->getHttpHeaders()
		);
		$this->withStatus(
			$response->getStatusCode(), 
			$response->getReasonPhrase()
		);

		return $this;
	}

	/**
	 * Get response redirect instance
	 * @return \Repository\Component\Http\ResponseRedirect
	 */
	public function redirect()
	{
		return ResponseRedirect::getInstance();
	}
	
	/**
	 * Convert response to string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->getBody()->getContents();
	}
}
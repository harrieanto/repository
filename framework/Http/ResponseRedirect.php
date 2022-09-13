<?php
namespace Repository\Component\Http;

use Psr\Http\Message\UriInterface;
use Repository\Component\Support\Statics\Request;
use Repository\Component\Support\Statics\Session;
use Repository\Component\Support\Statics\Response;

/**
 * Response Redirect.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ResponseRedirect
{
	/**
	 * Redirect target path name
	 * @var string $target
	 */
	private static $target = '/';

	private static $instance = null;

	/**
	 * Get Response Redirect Instance
	 * 
	 * @return \Repository\Component\Http\ResponseRedirect
	 */
	public static function getInstance()
	{
		return new static;
	}

	/**
	 * Redirect URL
	 * 
	 * @param  string  $url
	 * @param  integer $delay
	 * 
	 * @return Meta name when header was sent and send new header when header sent not yet
	 */
	public static function redirect($target, int $delay = 0)
	{
		//Bind real url that want be redirect
		$target = trim($target, '/');

		//When header not yet sent before then send new header with temporary redirector
		if (!headers_sent()) {
			//move temporary
			header(sprintf("%s:%d;%s=%s", "refresh", $delay, "url", $target), true);
			Response::withStatus(302);
			
			return;
		}

		//Print meta http-equip when header has sent
		echo "<meta http-equip=\"refresh\" content=\"2;url={$target}\">\r\n";
	}

	/**
	 * Fallback response to the previous request with flash messages
	 * 
	 * @param null|string $key The key of flash message
	 * @param string|array $messages
	 * 
	 * @return void
	 */	
	public static function back($key = null, $messages = array())
	{
		if ($key !== null) {
			Session::flash($key, $messages);
		}

		$target = Request::getPreviousUri(true);

		self::redirect($target);
	}

	/**
	 * Redirect response to the specific url
	 * 
	 * @param string $target Target path name
	 * 
	 * @return void
	 */	
	public static function to(UriInterface $target)
	{
		$target = $target->getUri();

		self::redirect($target);
	}

	/**
	 * Redirect response with flash message to the specific view target
	 * 
	 * @param string $key The key of flash message
	 * @param string|array $messages
	 * 
	 * @return void
	 */
	public static function with($key, $messages)
	{
		Session::flash($key, $messages);

		self::redirect(self::getTarget());
	}

	/**
	 * Set target redirection
	 * 
	 * @param string $target Target path name
	 * 
	 * @return \Repository\Component\Http\ResponseRedirect
	 */
	public static function make(string $target)
	{
		$target = mb_strtolower(trim($target, '/'));
		
		$uri = new Uri;

		$scheme = "http";

		if (Request::isSecure()) {
			$scheme = "https";
		}
		
		$uri->withScheme($scheme);
		$uri->withHost(Request::getHost());
		$uri->withPort(Request::getPort());
		$uri->withPath($target);

		self::$target = $uri->getUri();
		
		return new self;
	}

	/**
	 * Get target redirection
	 * 
	 * @return string
	 */
	public static function getTarget()
	{
		return self::$target;
	}
}

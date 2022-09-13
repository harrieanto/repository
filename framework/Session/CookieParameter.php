<?php
namespace Repository\Component\Session;

use Repository\Component\Http\Cookie;
use Psr\Http\Message\ResponseInterface;
use Repository\Component\Config\Config;

/**
 * Session Cookie Parameter.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class CookieParameter
{
	/**
	 * @var \Psr\Http\Message\ResponseInterface $response
	 */
	private $response;

	/**
	 * @var \Repository\Component\Http\Cookie $cookie
	 */
	private $cookie;
	
	/**
	 * @param \Psr\Http\Messages\ResponseInterface $response
	 * @param \Repository\Component\Contracts\Session\SessionInterface
	 * @param \Repository\Component\Http\Cookie $cookie
	 */	
	public function __construct(ResponseInterface $response, Cookie $cookie)
	{
		$this->response = $response;
		$this->cookie = $cookie;
	}

	/**
	 * Send initialized cookie
	 * 
	 * @return void
	 */	
	public function sendCookie()
	{
		// Send the cookies
		foreach ($this->response->getCookies(true) as $cookie) {
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
	}

	/**
	 * Set cookie value
	 *
	 * @param \Repository\Component\Http\Cookie $cookie
	 * @param mixed $value
	 * 
	 * @return void
	 */	
	public function setCookie($value = '')
	{
		$cookie = $this->cookie;
		$cookie->setName($this->getCookieName());
		$cookie->setValue($value);
		$cookie->setPath($this->getCookiePath());
		$cookie->setDomain($this->getCookieDomain());
		$cookie->setSameSite($this->getCookieSameSite());
		$cookie->setSecure($this->isSecure());
		$cookie->setExpiration($this->getExpiration());
		$cookie->setHttpOnly($this->isHttpOnly());

		$this->response->setCookie($cookie);
		$this->sendCookie();
	}

	/**
	 * Remove cookie by the given cookie name
	 * 
	 * @return void
	 */	
	public function delete($cookieName)
	{
		//Turn off session cookie
		$this->response->deleteCookie($cookieName, $this
			->getCookiePath(), $this
			->getCookieDomain(), $this
			->isSecure(), $this
			->isHttpOnly()
		);
		
		$this->sendCookie();
	}

	/**
	 * Get cookie name from file configuration
	 *
	 * @return string
	 */
	public function getCookieName()
	{
		$cookieName = $this->getSessionParameter('cookie_name');
		
		return $cookieName;
	}

	/**
	 * Get cookie path from file configuration
	 *
	 * @return string
	 */
	public function getCookiePath()
	{
		$cookiePath = $this->getSessionParameter('path_of_cookie');
		
		return $cookiePath;
	}

	/**
	 * Determine if the cookie secure
	 *
	 * @return bool
	 */
	public function isSecure()
	{
		$isSecure = $this->getSessionParameter('secure');
		
		return $isSecure;
	}

	/**
	 * Get cookie same site value
	 *
	 * @return bool
	 */
	public function getCookieSameSite()
	{
		return $this->getSessionParameter('same_site');
	}

	/**
	 * Determine if the cookie available on the http only
	 *
	 * @return bool
	 */
	public function isHttpOnly()
	{
		if ($this->isSecure()) return true;
		
		return false;
	}

	/**
	 * Get cookie domain from file configuration
	 *
	 * @return string
	 */
	public function getCookieDomain()
	{
		$cookieDomain = $this->getSessionParameter('domain');
		
		return $cookieDomain;
	}

	/**
	 * Get cookie expiration from file configuration
	 *
	 * @return string
	 */
	public function getExpiration()
	{
		$expiration = $this->getSessionParameter('expires');
		
		return time()+$expiration;
	}

	/**
	 * Get session configuration by the given key
	 * 
	 * @param string $group
	 *
	 * @return string|array
	 */
	public function getSessionParameter($group)
	{
		$group  = Config::get('session')[$group];
		return $group;
	}
}
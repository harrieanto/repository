<?php
namespace Repository\Component\Http;

use DateTime;

/**
 * Cookie.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Cookie
{
	/** 
	 * The name of the cookie
	 * @var string  $name
	 */
	private $name = '';

	/**
	 * The value of the cookie
	 * @var mixed  $value
	 */
	private $value = '';

	/**
	 * The expiration timestamp of the cookie
	 * @var int $expiration
	 */
	private $expiration = null;

	/**
	 * The path the cookie is valid on
	 * @var string $path
	 */
	private $path = '/';

	/**
	 * The domain the cookie is valid on
	 * @var string $domain
	 */
	private $domain = '';

	/**
	 * The cookie same site value
	 * @var string $sameSite
	 */
	private $sameSite = '';

	/**
	 * Whether or not this cookie is on HTTPS
	 * @var bool $isSecure
	 */
	private $isSecure = false;

	/**
	 * Whether or not this cookie is HTTP only
	 * @var bool $isHttpOnly
	 */
	private $isHttpOnly = true;

	/**
	 * @param string $name The name of the cookie
	 * @param mixed $value The value of the cookie
	 * @param DateTime|int $expiration The expiration of the cookie
	 * @param string $path The path the cookie is valid on
	 * @param string $domain The domain the cookie is valid on
	 * @param bool $isSecure Whether or not this cookie is on HTTPS
	 * @param bool $isHttpOnly Whether or not this cookie is HTTP only
	 */
	public function __construct(
		string $name = '',
		$value = '',
		$expiration = '',
		string $path = '/',
		string $domain = '', 
		string $samesite = 'None', 
		bool $isSecure = false,
		bool $isHttpOnly = true
	) {
		$this->name = $name;
		$this->value = $value;
		$this->setExpiration($expiration);
		$this->path = $path;
		$this->domain = $domain;
		$this->sameSite = $samesite;
		$this->isSecure = $isSecure;
		$this->isHttpOnly = $isHttpOnly;
	}

	/**
	 * @return string
	 */
	public function getDomain() : string
	{
		return $this->domain;
	}

	/**
	 * @return string
	 */
	public function getSameSite()
	{
		return $this->sameSite;
	}

	/**
	 * @return int
	 */
	public function getExpiration() : int
	{
		return $this->expiration;
	}

	/**
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getPath() : string
	{
		return $this->path;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return bool
	 */
	public function isHttpOnly() : bool
	{
		return $this->isHttpOnly;
	}

	/**
	 * @return bool
	 */
	public function isSecure() : bool
	{
		return $this->isSecure;
	}

	/**
	 * @param string $domain
	 */
	public function setDomain(string $domain)
	{
		$this->domain = $domain;
	}

	/**
	 * @param string $value
	 */
	public function setSameSite(string $value)
	{
		$this->sameSite = $value;
	}

	/**
	 * @param DateTime|int $expiration
	 */
	public function setExpiration($expiration)
	{
		if ($expiration instanceof DateTime) {
			$expiration = (int)$expiration->format('U');
		}

		$this->expiration = $expiration;
	}

	/**
	 * @param bool $isHttpOnly
	 */
	public function setHttpOnly(bool $isHttpOnly)
	{
		$this->isHttpOnly = $isHttpOnly;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @param string $path
	 */
	public function setPath(string $path)
	{
		$this->path = $path;
	}

	/**
	 * @param bool $isSecure
	 */
	public function setSecure(bool $isSecure)
	{
		$this->isSecure = $isSecure;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
}
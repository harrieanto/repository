<?php
namespace Repository\Component\Http\Middlewares;

use Psr\Http\Message\RequestInterface;
use Repository\Component\Http\Request;
use Repository\Component\Contracts\Session\SessionInterface;

/**
 * CSRF TOKEN Checker.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
; * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class CsrfTokenChecker
{
	/** The token field name **/
	const TOKEN_INPUT_FIELD = '_token';
	
	/**
	 * Session instance
	 * @var \Repository\Component\Contracts\Session\SessionInterface $session
	 */
	private $session;

	/**
	 * Request instance
	 * @var \Psr\Http\Message\RequestInterface $request
	 */
	private $request;

	/**
	 * @param \Repository\Component\Contracts\Session\SessionInterface $session
	 * @param \Psr\Http\Message\RequestInterface $request
	 */
	public function __construct(SessionInterface $session, RequestInterface $request)
	{
		$this->session = $session;
		$this->request = $request;
	}

	/**
	 * Determine if the token is valid
	 * @return bool false when token is invalid, true otherwise
	 */
	public function tokenIsValid(): bool
	{
		if (!$this->session->has(self::TOKEN_INPUT_FIELD)) {
			$randomToken = bin2hex(openssl_random_pseudo_bytes(16));
			$this->session->set(self::TOKEN_INPUT_FIELD, $randomToken);
		}
		
		if ($this->isTokenShouldNotBeChecked()) {
			return true;
		}
		
		$token = (array) $this->request->getParsedBody();
		
		if (array_key_exists(self::TOKEN_INPUT_FIELD, $token)) {
			$token = $token[self::TOKEN_INPUT_FIELD];
		} else {
			$token = '';
		}

		if (empty($token)) {
			$token = $this->request->getHeaderLine('X-CSRF-TOKEN');
		}

		if (empty($token)) {
			$token = $this->request->getHeaderLine('X-XSRF-TOKEN');
		}
		
		if (empty($token)) {
			return false;
		}
		
		return hash_equals($this->session->get(self::TOKEN_INPUT_FIELD), $token);
	}

	/**
	 * Determine if the token should be checked on each request
	 * @return bool true when token should be checked, false otherwise
	 */	
	public function isTokenShouldNotBeChecked()
	{
		$shouldNotBeChecked = array(Request::GET, Request::HEAD, Request::OPTIONS);
		return in_array($this->request->getRequestMethod(), $shouldNotBeChecked);
	}
}
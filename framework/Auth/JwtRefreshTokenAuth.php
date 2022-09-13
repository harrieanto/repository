<?php
namespace Repository\Component\Auth;

use Repository\Component\Auth\Jwt\Repository\JwtRepository;
use Repository\Component\Auth\Jwt\Verification\JwtVerifier;
use Repository\Component\Contracts\Auth\CredentialInterface;
use Repository\Component\Auth\Jwt\Verification\Context as VerifierContext;

/**
 * Jwt Refresh Token Authenticator.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JwtRefreshTokenAuth extends JwtAuth
{
	/**
	 * @param \Repository\Component\Auth\Jwt\Repository\JwtReposiotry $repository
	 * @param \Repository\Component\Auth\Jwt\Verification\JwtVerifier $verifier
	 * @param \Repository\Component\Auth\Jwt\Context $context
	 */
	public function __construct(
		JwtRepository $repository, 
		JwtVerifier $verifier, 
		VerifierContext $context)
	{
		parent::__construct($repository, $verifier, $context);
	}
	
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Auth\AuthInterface::authenticate()
	 */
	public function authenticate(CredentialInterface $credential, &$error)
	{
		if (!parent::authenticate($credential, $error)) {
			return false;
		}

		return true;
	}
}
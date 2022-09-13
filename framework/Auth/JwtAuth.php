<?php
namespace Repository\Component\Auth;

use Repository\Component\Auth\Jwt\JwtToken;
use Repository\Component\Contracts\Auth\AuthInterface;
use Repository\Component\Auth\Jwt\Repository\JwtRepository;
use Repository\Component\Auth\Jwt\Verification\JwtVerifier;
use Repository\Component\Contracts\Auth\CredentialInterface;
use Repository\Component\Auth\Jwt\Verification\Context as VerifierContext;

/**
 * Jwt Authenticator.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JwtAuth implements AuthInterface
{
	/**
	 * The Jwt repository instance
	 * @var \Repository\Component\Auth\Jwt\Repository\JwtReposiotry $jwtRepository
	 */
	private $jwtRepository;

	/**
	 * The Jwt verifier factory instance
	 * @var \Repository\Component\Auth\Jwt\Verification\JwtVerifier $verifier
	 */
	private $verifier;

	/**
	 * The Jwt verifier context instance
	 * @var \Repository\Component\Auth\Jwt\Verification\Context $context
	 */
	private $context;

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
		$this->jwtRepository = $repository;
		$this->verifier = $verifier;
		$this->context = $context;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Auth\AuthInterface::authenticate()
	 */	
	public function authenticate(CredentialInterface $credential, &$error)
	{
		//Get signed jwt token from credential payload
		$signedJwt = $credential->getValue('token');
		//If signed jwt is not available
		//Then we can't do anything except return false and populate error type
		if ($signedJwt === null) {
			$error = AuthErrorTypes::CREDENTIAL_IS_MISSING;
			return false;
		}
		
		//If the token is filled we now check it in our data source
		//Determine if it match one or not
		//And put further decission to the given signed token
		$signedJwts = $this->jwtRepository->getBySignedJwt($signedJwt);
		
		//Here we can handle miss match signed jwt token		
		if (!$signedJwts || $signedJwts === null) {
			$error = AuthErrorTypes::SIGNED_JWT_TOKEN_NOT_FOUND;
			return false;
		}

		//If the given credential token is valid one
		//Then we can read the token and verify it
		//to decide whether or not the token is still valid to access our application
		$signedJwt = JwtToken::createFromString($signedJwt);

		$error = array();

		if (!$this->verifier->verify($signedJwt, $this->context, $error)) {
			return false;
		}
		
		return true;
	}
}
<?php
namespace Repository\Component\Auth\Jwt\Verification;

use Repository\Component\Auth\Jwt\JwtToken;
use Repository\Component\Contracts\Auth\JwtVerifierInterface;

/**
 * The Jwt Issuer Payload Verifier.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class IssuerVerifier implements JwtVerifierInterface
{
	/**
	 * @var null|string $issuer
	 */
	private $issuer;
	
	/**
	 * @param null|string $issuer
	 */
	public function __construct($issuer = null)
	{
		$this->issuer = $issuer;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Auth\JwtVerifierInterface
	 */
	public function verify(JwtToken $jwt, &$error = null)
	{
		$issuer = $jwt->getPayload()->getIssuer();
		
		if ($this->issuer !== $issuer) {
			$error = VerifierErrorTypes::INVALID_ISSUER;
			return false;
		}
		
		return true;
	}
}
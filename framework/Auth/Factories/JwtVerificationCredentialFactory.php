<?php
namespace Repository\Component\Auth\Factories;

use InvalidArgumentException;
use Repository\Component\Auth\Credential;
use Repository\Component\Auth\CredentialTypes;

/**
 * Jwt Verifiction Credential  Factory - Create Credential Payload for Verification Purpose.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JwtVerificationCredentialFactory
{
	/**
	 * Creating jwt token credential payload
	 * 
	 * @param array $claims The custom jwt verification claims
	 * 
	 * @return  \repository\Component\Auth\Credential
	 */
	public function createCredentialPayload(array $claims)
	{
		if (!isset($claims['token'])) {
			throw new InvalidArgumentException('Credential verification payload must be contains token at least');
		}

		return new Credential($this->getCredentialType(), $claims);
	}

	/**
	 * Get credential type of the jwt request token
	 * 
	 * @return  string The type of mentioned credential
	 */
	public function getCredentialType()
	{
		return CredentialTypes::SIGNED_TOKEN_VERIFICATION;
	}
}
<?php
namespace Repository\Component\Auth\Jwt\Verification;

use DateTime;
use Repository\Component\Auth\Jwt\JwtToken;
use Repository\Component\Contracts\Auth\JwtVerifierInterface;

/**
 * The Jwt Expiration Verifier.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ExpirationVerifier implements JwtVerifierInterface
{
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Auth\JwtVerifierInterface
	 */
	public function verify(JwtToken $jwt, &$error)
	{
		$expired = $jwt->getPayload()->getExpiredAt();
		
		$expired = $expired->getTimestamp();
		
		$now = new DateTime;

		if ($expired < $now->getTimestamp()) {
			$error = VerifierErrorTypes::TOKEN_EXPIRED;
			return false;
		}
		
		return true;
	}
}
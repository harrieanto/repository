<?php
namespace Repository\Component\Auth\Jwt\Verification;

use DateTime;
use Repository\Component\Auth\Jwt\JwtToken;
use Repository\Component\Contracts\Auth\JwtVerifierInterface;

/**
 * Not Before Time Verifier.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class NotBeforeTimeVerifier implements JwtVerifierInterface
{
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Auth\JwtVerifierInterface
	 */
	public function verify(JwtToken $jwt, &$error)
	{
		$started = $jwt->getPayload()->getValidFrom();
		
		if (!$started instanceof DateTime) {
			return true;
		}

		$started = $started->getTimestamp();
		
		$now = new DateTime;

		if ($started > $now->getTimestamp()) {
			$error = VerifierErrorTypes::TOKEN_USED_BEFORE_THE_TIME;
			return false;
		}
		
		return true;
	}
}
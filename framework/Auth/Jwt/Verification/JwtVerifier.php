<?php
namespace Repository\Component\Auth\Jwt\Verification;

use Repository\Component\Auth\Jwt\JwtToken;

/**
 * The Jwt Verifier Factory.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JwtVerifier
{
	/**
	 * Verify signed jwt token by the given verification context
	 * 
	 * @param \Repository\Component\Auth\Jwt\JwtToken $jwt
	 * @param \Repository\Component\Auth\Jwt\Verification\Context $context
	 * @param array $errors Refereced error type/message
	 *  
	 * @return bool true When passed, false otherwise
	 */
	public function verify(JwtToken $jwt, Context $context, array &$errors = array())
	{
		$verifiers = array(
			new IssuerVerifier($context->getIssuer()), 
			new AudienceVerifier($context->getAudience()), 
			new SubjectVerifier($context->getSubject()), 
			new SignatureVerifier($context->getSigner()), 
			new ExpirationVerifier(), 
			new NotBeforeTimeVerifier(), 
		);
		
		$isValid = true;

		foreach ($verifiers as $verifier) {
			$error = '';

			if (!$verifier->verify($jwt, $error)) {
				$isValid = false;
				$errors[] = $error;
			}
		}

		return $isValid;
	}
}
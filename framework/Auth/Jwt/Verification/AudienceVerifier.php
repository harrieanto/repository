<?php
namespace Repository\Component\Auth\Jwt\Verification;

use Repository\Component\Auth\Jwt\JwtToken;
use Repository\Component\Contracts\Auth\JwtVerifierInterface;

/**
 * The Audience Verifier.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class AudienceVerifier implements JwtVerifierInterface
{
	/**
	 * The audiences
	 * @var string|array $audience
	 */
	private $audience;

	/**
	 * @param string|array $audience
	 */
	public function __construct($audience = array())
	{
		if (!is_array($audience)) {
			$audience = (array) $audience;
		}
		
		$this->audience = $audience;
	}
	
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Auth\JwtVerifierInterface
	 */
	public function verify(JwtToken $jwt, &$error)
	{
		$audience = $jwt->getPayload()->getAudience();
		
		if (!is_array($audience)) {
			$audience = (array) $audience;
		}
		
		if (count($this->audience) === 0) {
			return true;
		}
		
		$audiences = array_intersect($audience, $this->audience);
		
		if (count($audiences) === 0) {
			$error = VerifierErrorTypes::INVALID_AUDIENCE;
			return false;
		}
		
		return true;
	}
}
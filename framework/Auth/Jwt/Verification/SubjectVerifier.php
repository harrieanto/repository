<?php
namespace Repository\Component\Auth\Jwt\Verification;

use Repository\Component\Auth\Jwt\JwtToken;
use Repository\Component\Contracts\Auth\JwtVerifierInterface;

/**
 * The Jwt Subject Verifier.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class SubjectVerifier implements JwtVerifierInterface
{
	/**
	 * The subject name
	 * @var string $subject
	 */
	private $subject;
	
	/**
	 * @param null|string $subject
	 */
	public function __construct($subject = null)
	{
		$this->subject = $subject;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Auth\JwtVerifierInterface
	 */
	public function verify(JwtToken $jwt, &$error = null)
	{
		$subject = $jwt->getPayload()->getSubject();
		
		if ($this->subject === null) {
			return true;
		}
		
		if ($this->subject !== $subject) {
			$error = VerifierErrorTypes::INVALID_SUBJECT;
			return false;
		}
		
		return true;
	}
}
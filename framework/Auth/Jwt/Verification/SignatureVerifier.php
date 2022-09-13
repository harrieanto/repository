<?php
namespace Repository\Component\Auth\Jwt\Verification;

use Repository\Component\Auth\Jwt\JwtToken;
use Repository\Component\Auth\Jwt\Signer\Algorithm;
use Repository\Component\Contracts\Auth\SignerInterface;
use Repository\Component\Contracts\Auth\JwtVerifierInterface;

/**
 * The Jwt Signature Verifier.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class SignatureVerifier implements JwtVerifierInterface
{
	/**
	 * The signer instance
	 * @var \Repository\Component\Auth\Contracts\Auth\SignerInterface $signer
	 */
	private $signer;

	/**
	 * @param \Repository\Component\Auth\Contracts\Auth\SignerInterface $signer
	 */
	public function __construct(SignerInterface $signer)
	{
		$this->signer = $signer;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Auth\JwtVerifierInterface
	 */
	public function verify(JwtToken $jwt, &$error = null)
	{
		$signature = $jwt->getSignature();

		if ($signature === '') {
			$error = VerifierErrorTypes::INVALID_SIGNATURE;
			return false;
		}
		
		$algorithm = $jwt->getHeader()->getAlgorithm();
		$algorithm = new Algorithm($algorithm);

		if (!$algorithm->isAvailable()) {
			$error = VerifierErrorTypes::INVALID_ALGORITHM;
			return false;
		}

		if (!$this->signer->verify($signature, $jwt->getUnsignedToken())) {
			$error = VerifierErrorTypes::MISS_MATCH_SIGNATURE;
			return false;
		}
		return true;
	}
}
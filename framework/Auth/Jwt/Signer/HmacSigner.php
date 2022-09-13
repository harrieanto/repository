<?php
namespace Repository\Component\Auth\Jwt\Signer;

use InvalidArgumentException;
use Repository\Component\Support\Encoder;
use Repository\Component\Contracts\Auth\SignerInterface;

/**
 * Symmetric HMAC Signer.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class HmacSigner implements SignerInterface
{
	/**
	 * The algorithm type
	 * @var string $algorithm
	 */
	private $algorithm;

	/**
	 * The public key
	 * @var string $publicKey
	 */
	private $publicKey;
	
	/**
	 * @param string $algorithm
	 * @param string $publicKey
	 */
	public function __construct($algorithm, $publicKey)
	{
		$this->algorithm = $algorithm;
		$this->publicKey = $publicKey;
	}

	/**
	 * Signed the given payload
	 * 
	 * @param string $payload The payload that we want signed
	 * 
	 * @return string Signed payload
	 */
	public function sign(string $payload)
	{
		$algorithm = $this->getAlgorithm($this->algorithm);
		$signed = hash_hmac($algorithm, $payload, $this->publicKey, true);
		
		return $signed;
	}

	/**
	 * Determine if the given signature is match with the given payload
	 * 
	 * @param string $signature The signed payload
	 * @param string $payload The unsigned payload
	 * 
	 * @return bool True when the signature is correct
	 * False otherwise
	 */	
	public function verify(string $signature, string $payload)
	{
		return hash_equals($signature, $this->sign($payload));
	}

	/**
	 * Get algorithm type
	 * 
	 * @param string $type
	 * 
	 * @throw \InvalidArgumentException When the given algorithm not available
	 * 
	 * @return string
	 */	
	public function getAlgorithm($type)
	{
		if ($type[0] === 'H') {
			$hash = new Algorithm($type);
			
			if (!$hash->isAvailable()) {
				throw new InvalidArgumentException("Algorithm [$type] is not supported");
			}
			
			return $hash->get($type);
		}
		
		throw new InvalidArgumentException("Invalid algorithm [$type]");
	}
}
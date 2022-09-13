<?php
namespace Repository\Component\Auth\Jwt\Signer;

use InvalidArgumentException;
use Repository\Component\Contracts\Auth\SignerInterface;

/**
 * Asymmetric Public-Private Key Signer.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class RsaSsaSigner implements SignerInterface
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
	 * The private key
	 * @var string $privateKey
	 */
	private $privateKey;

	/**
	 * @param string $algorithm
	 * @param string $privateKey
	 * @param string $publicKey
	 */
	public function __construct($algorithm, $privateKey, $publicKey)
	{
		$this->algorithm = strtoupper($algorithm);
		$this->privateKey = $privateKey;
		$this->publicKey = $publicKey;
	}

	/**
	 * Signed the given payload
	 * 
	 * @param string $payload The payload that we want signed
	 *  
	 * @trhow \InvalidArgumentException When signing process failed
	 * 
	 * @return string Signed payload
	 */
	public function sign(string $payload)
	{
		$signature = '';
		$algorithm = $this->getAlgorithm($this->algorithm);

		if (!openssl_sign($payload, $signature, $this->privateKey, $algorithm)) {
			throw new InvalidArgumentException("Couldn't signing data");
		}
		
		return $signature;
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
		if ($signature === '') {
			return false;
		}

		$algorithm = $this->getAlgorithm($this->algorithm);
		return openssl_verify($payload, $signature, $this->publicKey, $algorithm) === 1;
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
		if ($type[0] === 'R') {
			$hash = new Algorithm($type);
			
			if (!$hash->isAvailable()) {
				throw new InvalidArgumentException("Algorithm [$type] is not supported");
			}
			
			return $hash->get($type);
		}

		throw new InvalidArgumentException("Invalid algorithm [$type]");
	}
}
<?php
namespace Repository\Component\Auth\Jwt;

use DateTime;
use InvalidArgumentException;
use Repository\Component\Support\Encoder;

/**
 * Jwt Token Generator.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JwtToken
{
	/**
	 * Jwt header instance
	 * @var \Repository\Component\Auth\Jwt\JwtHeader $header
	 */
	private $header;

	/**
	 * Jwt payload instance
	 * @var \Repository\Component\Auth\Jwt\JwtPayload $payload
	 */
	private $payload;

	/**
	 * The encoded signature
	 * @var string $signature
	 */
	private $signature;

	/**
	 * Create Jwt instance
	 * @param \Repository\Component\Auth\Jwt\JwtHeader $header
	 * @param \Repository\Component\Auth\Jwt\JwtPayload $payload
	 * @param string $signature
	 */
	public function __construct(
		JwtHeader $header, 
		JwtPayload $payload, 
		string $signature)
	{
		$this->header = $header;
		$this->payload = $payload;
		$this->signature = $signature;
	}

	/**
	 * Create Jwt token by the header, payload and signature specification
	 *  
	 * @return string The fresh jwt token
	 */
	public function createToken()
	{
		$token = array(
			$this->header->encode(), 
			$this->payload->encode(), 
			$this->signature, 
		);

		return implode('.', $token);
	}

	/**
	 * Create Jwt token by the given token string
	 *  
	 * @return string The re-builded token 
	 */
	public static function createFromString($token)
	{
		$segments = explode('.', $token);

		if (count($segments) !== 3) {
			$ex = "Invalid Token. There's must be contains 3(three) segment";
			throw new InvalidArgumentException($ex);
		}

		list($header, $payload, $signature) = $segments;
		
		$decodedHeader = Encoder::base64UrlDecode($header);
		$decodedHeader = json_decode($decodedHeader, true);
		$decodedPayload = Encoder::base64UrlDecode($payload);
		$decodedPayload = json_decode($decodedPayload, true);
		$decodedSignature = Encoder::base64UrlDecode($signature);

		if ($header === null) {
			$ex = "Invalid Header";
			throw new InvalidArgumentException($ex);
		}

		if ($payload === null) {
			$ex = "Invalid Payload";
			throw new InvalidArgumentException($ex);
		}

		if ($signature === null) {
			$ex = "Invalid Signature";
			throw new InvalidArgumentException($ex);
		}

		if (!isset($decodedHeader['alg'])) {
			$ex = "No algorithm set in header";
			throw new InvalidArgumentException($ex);
		}

		$header = new JwtHeader($decodedHeader['alg'], $decodedHeader);
		$payload = new JwtPayload();

		if (is_array($decodedPayload)) {
			foreach ($decodedPayload as $name => $value) {
				$payload->add($name, $value);
			}
		}
		
		return new self($header, $payload, $signature);
	}
	
	/**
	 * Get usngined jwt token
	 * 
	 * @return string Unsigned jwt
	 */
	public function getUnsignedToken()
	{
		$unsigned = explode('.', $this->createToken());

		array_pop($unsigned);

		return implode('.', $unsigned);
	}

	/**
 	 * Get jwt header
 	 *  
 	 * @return \Repository\Component\Auth\Jwt\JwtHeader
	 */
	public function getHeader()
	{
		return $this->header;
	}

	/**
 	 * Get jwt payload
 	 *  
 	 * @return \Repository\Component\Auth\Jwt\JwtPayload
	 */
	public function getPayload()
	{
		return $this->payload;
	}

	/**
 	 * Get jwt signature
 	 *  
 	 * @return \Repository\Component\Auth\Jwt\JwtSignature
	 */	
	public function getSignature()
	{
		$signature = Encoder::base64UrlDecode($this->signature);
		
		return $signature;
	}
}
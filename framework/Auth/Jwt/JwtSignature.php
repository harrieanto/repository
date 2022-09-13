<?php
namespace Repository\Component\Auth\Jwt;

use Repository\Component\Support\Encoder;
use Repository\Component\Contracts\Auth\SignerInterface;

/**
 * Jwt Signature.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JwtSignature
{
	/**
	 * JwtHeader instance
	 * @var \Repository\Component\Auth\JwtHeader $header
	 */
	private $header;

	/**
	 * JwtPayload instance
	 * @var \Repository\Component\Auth\JwtPayload $payload
	 */	
	private $payload;

	/**
	 * Signer instance
	 * @var \Repository\Component\Contracts\Auth\Signer\SignerInterface $signer
	 */	
	private $signer;

	/**
	 * @param \Repository\Component\Contracts\Auth\SignerInterface $signer
	 * @param \Repository\Component\Auth\JwtHeader $header
	 * @param \Repository\Component\Auth\JwtPayload $payload
	 */
	public function __construct(SignerInterface $signer, JwtHeader $header, JwtPayload $payload)
	{
		$this->signer = $signer;
		$this->header = $header;
		$this->payload = $payload;
	}

	/**
	 * Generate signature by header and payload and encode it
	 * 
	 * @return string The encoded signature
	 */
	public function encode()
	{
		$data = array(
			$this->header->encode(), 
			$this->payload->encode(), 
		);

		$data = implode('.', $data);
		
		$signature = $this->signer->sign($data);
		
		return Encoder::base64UrlEncode($signature);
	}
}
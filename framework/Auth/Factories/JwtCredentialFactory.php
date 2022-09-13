<?php
namespace Repository\Component\Auth\Factories;

use DateTime;
use Repository\Component\Auth\Credential;
use Repository\Component\Auth\Jwt\JwtToken;
use Repository\Component\Auth\Jwt\JwtHeader;
use Repository\Component\Auth\Jwt\JwtPayload;
use Repository\Component\Auth\CredentialTypes;
use Repository\Component\Auth\Jwt\JwtSignature;
use Repository\Component\Contracts\Auth\SignerInterface;

/**
 * Jwt Credential Factory.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
abstract class JwtCredentialFactory
{
	/**
	 * The jwt audience claim
	 * @var string|array $audience
	 */
	protected $audience;

	/**
	 * The jwt issuer claim
	 * @var string $issuer
	 */
	protected $issuer;

	/**
	 * The jwt started time claim where the token can be use.
	 * @var DateTime $started
	 */
	protected $started;

	/**
	 * The jwt expired time claim where the token no longer can be used.
	 * @var DateTime $started
	 */
	protected $expired;

	/**
	 * @param \Repository\Component\Contracts\Auth\Signatureinterface $signer
	 * @param string $issuer
	 * @param string|array $audience
	 * @param DateTime $started
	 * @param DateTime $expired
	 */
	public function __construct(
		SignerInterface $signer, 
		$issuer, 
		$audience, 
		DateTime $started, 
		DateTime $expired)
	{
		$this->signer = $signer;
		$this->issuer = $issuer;
		$this->audience = $audience;
		$this->started = $started;
		$this->expired = $expired;
	}

	/**
	 * Creating jwt token credential payload
	 * 
	 * @param array $claims The custom jwt payload claims
	 * 
	 * @return  \repository\Component\Auth\Credential
	 */
	public function createCredentialPayload(array $claims)
	{
		$token = $this->generateSignedToken($claims);
		
		return new Credential($this->getCredentialType(), array('token' => $token));
	}

	/**
	 * Get credential type of the jwt request token
	 * So we have separation of concerns for handling jwt token request such as:
	 * access the token for first time or refresh the token before the expired time raised/exceeded
	 * 
	 * This method must implemented by child class
	 * 
	 * @return  string The type of mentioned credential
	 */	
	abstract function getCredentialType();

	/**
	 * Add custom payload claims
	 * 
	 * @param \Repository\Component\Auth\Jwt\JwtPayload $payload
	 * @param array $claims The custom jwt payload claims
	 * 
	 * @return  \Repository\Component\Auth\Jwt\JwtPayload
	 */	
	protected function addCustomClaims(JwtPayload $payload, array $claims)
	{
		foreach ($claims as $claim => $value) {
			$payload->add($claim, $value);
		}

		return $payload;
	}

	/**
	 * Create jwt signature by the given unsigned token header and payload
	 * 
	 * @param \Repository\Component\Auth\Jwt\JwtHeader $header
	 * @param \Repository\Component\Auth\Jwt\JwtPayload $payload
	 * 
	 * @return  string The encoded jwt signature
	 */	
	protected function createJwtSignature(JwtHeader $header, JwtPayload $payload)
	{
		$signature = new JwtSignature($this->signer, $header, $payload);
		
		return $signature->encode();
	}

	/**
	 * Generate signed jwt token ny the given custom payload claims
	 * 
	 * @param array $claims
	 * 
	 * @return  string The signed jwt token
	 */	
	protected function generateSignedToken(array $claims = array())
	{
		$header = new JwtHeader;
		$payload = new JwtPayload;
		$payload->setAudience($this->audience);
		$payload->setIssuer($this->issuer);
		$payload->setIssuedAt(new DateTime);
		$payload->setValidFrom($this->started);
		$payload->setExpiredAt($this->expired);
		$payload = $this->addCustomClaims($payload, $claims);
		$signature = $this->createJwtSignature($header, $payload);
		$jwt = new JwtToken($header, $payload, $signature);
		
		return $jwt->createToken();
	}
}
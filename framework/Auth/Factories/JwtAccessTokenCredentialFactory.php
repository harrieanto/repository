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
 * Jwt Access Token Credential Factory.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JwtAccessTokenCredentialFactory extends JwtCredentialFactory
{
	/**
	 * @param \Repository\Component\Contracts\Auth\Signatureinterface $signer
	 * @param string $issuer
	 * @param string $clientid
	 * @param string $uriResource
	 * @param DateTime $started
	 * @param DateTime $expired
	 */
	public function __construct(
		SignerInterface $signer, 
		$issuer, 
		$clientId, 
		$uriResource, 
		DateTime $started, 
		DateTime $expired)
	{
		$audiences = [$clientId, $uriResource];

		parent::__construct(
			$signer, 
			$issuer, 
			$audiences, 
			$started, 
			$expired
		);
	}
	
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Auth\Factories\JwtCredentialFactory::getCredentialType()
	 */
	public function getCredentialType()
	{
		return CredentialTypes::ACCESS_TOKEN;
	}
}
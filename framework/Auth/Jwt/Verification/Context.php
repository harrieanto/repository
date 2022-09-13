<?php
namespace Repository\Component\Auth\Jwt\Verification;

use Repository\Component\Contracts\Auth\SignerInterface;

/**
 * The Verifier Context Object.
 * The Convenient Way to Setup Desired Content to be Verify
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Context
{
	/**
	 * @param \Repository\Componennt\Contracts\Auth\SignerInterface $signer
	 */
	private $signer;

	/**
	 * @param string $subject
	 */
	private $subject;

	/**
	 * @param string $issuer
	 */
	private $issuer;

	/**
	 * @param string $audience
	 */
	private $audience;
	
	/**
	 * @param \Repository\Componennt\Contracts\Auth\SignerInterface $signer
	 */
	public function __construct(SignerInterface $signer)
	{
		$this->signer = $signer;
	}

	/**
	 * @return \Repository\Component\Contracts\Auth\SignerInterface
	 */	
	public function getSigner()
	{
		return $this->signer;
	}

	/**
	 * Get subject payload
	 * 
	 * @return string
	 */	
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * Get issuer payload
	 * 
	 * @return string
	 */	
	public function getIssuer()
	{
		return $this->issuer;
	}

	/**
	 * Get audience payload
	 * 
	 * @return string|array
	 */		
	public function getAudience()
	{
		return $this->audience;
	}

	/**
	 * Set subject
	 * 
	 * @param string $subject
	 */		
	public function setSubject(string $subject)
	{
		$this->subject = $subject;
	}

	/**
	 * Set issuer
	 * 
	 * @param string $issuer
	 */
	public function setIssuer(string $issuer)
	{
		$this->issuer = $issuer;
	}

	/**
	 * Set audiences
	 * 
	 * @param string|array $subject
	 */		
	public function setAudience($audience)
	{
		$this->audience = $audience;
	}
}


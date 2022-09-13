<?php
namespace Repository\Component\Contracts\Auth;

/**
 * Token Signer Interface.
 * 
 * @package	 \Repository\Component\Contracts\Auth
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface SignerInterface
{
	/**
	 * Signed the given payload
	 * 
	 * @param string $payload The payload that we want signed
	 * 
	 * @return string Signed payload
	 */
	public function sign(string $payload);

	/**
	 * Determine if the given signature is match with the given payload
	 * 
	 * @param string $signature The signed payload
	 * @param string $payload The unsigned payload
	 * 
	 * @return bool True when the signature is correct
	 * False otherwise
	 */
	public function verify(string $signature, string $payload);

	/**
	 * Get algorithm type
	 * 
	 * @param string $type
	 * 
	 * @throw \InvalidArgumentException When the given algorithm not available
	 * 
	 * @return string
	 */
	public function getAlgorithm($type);
}
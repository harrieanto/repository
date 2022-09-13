<?php
namespace Repository\Component\Encryption;

/**
 * Common cipher method used for encryption.
 *
 * @package	  \Repository\Component\Encryption
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class CipherMethods
{
	/**
	 * The cipher method name
	 * @var array $chipher
	 */
	public $cipher;
	
	/**
	 * @param string The cipher name
	 */
	public function __construct($cipher)
	{
		$this->cipher = $cipher;
	}

	/**
	 * Determine if the cipher is available for encryption
	 * 
	 * @return bool
	 */	
	public function isAvailable()
	{
		if (in_array($this->cipher, $this->getAll()))
			return true;
		
		return false;
	}

	/**
	 * Get any available cipher method list
	 * 
	 * @return array
	 */
	public function getAll()
	{
		$ciphers = openssl_get_cipher_methods();
		
		return $ciphers;
	}

	/**
	 * Get passed cipher
	 * 
	 * @return string
	 */	
	public function getCipher()
	{
		return $this->cipher;
	}
}
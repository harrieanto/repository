<?php
namespace Repository\Component\Contracts\Encryption;

/**
 * Encryption Interface.
 * 
 * @package	  \Repository\Component\Contracts\Encryption
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface EncryptionInterface
{
	/**
	 * Create encryption data by the given value
	 * 
	 * @param string $value The value that want be encrypt
	 * 
	 * @throw \Repository\Component\Encryption\Exception\EncryptionException
	 * 
	 * @return string
	 */	
	public function encrypt($value);

	/**
	 * Get data value by the given encrypted value
	 * 
	 * @param string $value The encrypted value
	 * 
	 * @throw \Repository\Component\Encryption\Exception\EncryptionException
	 * 
	 * @return string
	 */	
	public function decrypt($value);
}
<?php
namespace Repository\Component\Session;

/**
 * Session Id Generator.
 * 
 * @package	  \Repository\Component\Session
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class IdGenerator
{
	/** The default length of session id **/
	const DEFAULT_LENGTH = 40;

	/** The minimal length of session id **/
	const MIN_LENGTH = 10;

	/** The maximal length of session id **/
	const MAX_LENGTH = 40;
	
	/**
	 * Generate session id by the given length
	 * 
	 * @param int $length The length of session id
	 * 
	 * @return string The session id
	 */
	public function generate(int $length = self::DEFAULT_LENGTH)
	{
		//Because N bytes become 2N character in bin2hex
		//Here we divide the length with 2
		$id = bin2hex(random_bytes(ceil($length / 2)));
		
		if ($length % 2 === 1) {
			$id = mb_substr($id, 1);
		}
		
		return $id;
	}

	/**
	 * Determine if the given session id is valid
	 * 
	 * @param string $id The session id
	 * 
	 * @return bool
	 */	
	public function isValid(string $id)
	{
		$pattern = sprintf("/^[a-z0-9]{%d,%d}$/i", self::MIN_LENGTH, self::MAX_LENGTH);
		
		return preg_match($pattern, $id);
	}
}
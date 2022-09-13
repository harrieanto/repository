<?php
namespace Repository\Component\Support;

use InvalidArgumentException;

/**
 * Json Helper.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Json
{
	/**
	 * Encode the given array data to the json format
	 * 
	 * @param array $data
	 * 
	 * @throw \InvalidArgumentException
	 * 
	 * @return string The encoded data
	 */
	public static function encode(array $data)
	{
		$encoded = json_encode($data);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new InvalidArgumentException('Failed to encode the json content');
		}
		
		return $encoded;
	}

	/**
	 * Decode the given json data to the array format
	 * 
	 * @param string $data
	 * 
	 * @throw \InvalidArgumentException
	 * 
	 * @return string The encoded data
	 */
	public static function decode(string $data)
	{
		$decoded = json_decode($data, true);
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new InvalidArgumentException('Failed to decode the json content');
		}
		
		return $decoded;
	}
}
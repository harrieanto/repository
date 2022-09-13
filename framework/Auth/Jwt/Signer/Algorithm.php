<?php
namespace Repository\Component\Auth\Jwt\Signer;

/**
 * Signature algorithm.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Algorithm
{
	/** The symmetric algorithm **/
	const HS256 = 'HS256';
	
	const HS384 = 'HS384';

	const HS512 = 'HS512';

	/** The asymmetric algorithm **/
	const RS256 = 'RS256';

	const RS384 = 'RS384';

	const RS512 = 'RS512';
	
	/**
	 * The algorithm container
	 * @var string $algorithm
	 */
	private static $algorithm = '';

	/**
	 * The algorithms list
	 * @var array $algorithms
	 */
	private static $algorithms = array(
		'HS256' => 'sha256', 
		'HS384' => 'sha384', 
		'HS512' => 'sha512', 
		'RS256' => OPENSSL_ALGO_SHA256, 
		'RS384' => OPENSSL_ALGO_SHA384, 
		'RS512' => OPENSSL_ALGO_SHA512, 
	);
	
	/**
	 * @param string $type The type of algorithm that we want to track
	 */
	public function __construct($type)
	{
		static::$algorithm = $type;
	}

	/**
	 * Get algorithm type by the given key
	 * 
	 * @param string $key
	 *  
	 * @return string
	 */
	public static function get($key)
	{
		return static::$algorithms[$key];
	}

	/**
	 * Get all listed algorithm
	 * 
	 * @return array
	 */
	public static function getAll()
	{
		return static::$algorithms;
	}

	/**
	 * Determine if the type is available/supported
	 * 
	 * @return bool
	 */
	public function isAVailable()
	{
		if (array_key_exists(static::$algorithm, static::$algorithms)) {
			return true;
		}
		
		return false;
	}
}

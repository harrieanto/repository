<?php
namespace Repository\Component\Hashing;

use Repository\Component\Contracts\Hashing\HashInterface;

/**
 * The Convenient Way Dealing with Password.
 *
 * @package	  \Repository\Component\Hashing
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Hash implements HashInterface
{
	/**
	 * Optional cost and salt provide for password_hash
	 * @var array $optionals
	 */
	public $optionals = array('cost' => 10, 'salt' => '');

	/**
	 * Make passowrd hash
	 * 
	 * @param  string  $password
	 * @param  string  $type Password type
	 * @param  array $optionals
	 * 
	 * @return string Hashed password
	 */
	public function make($password, $type = PASSWORD_BCRYPT, $optionals = array())
	{
		if (count($optionals) > 0)
			return password_hash($password, $type, $optional);

		return password_hash($password, $type);
	}

	/**
	 * Determine if the given password match with the given hash
	 * 
	 * @param  string $password
	 * @param  string $hash Hashed password
	 * 
	 * @return boolean
	 */
	public function isValid($password, $hash)
	{
		return password_verify($password, $hash);
	}

	/**
	 * Determine if the given password is expired
	 * 
	 * @param  string $password
	 * 
	 * @return boolean
	 */	
	public function isExpired($password)
	{
		if (password_need_rehash($password)) return true;
		
		return false;
	}

	/**
	 * Resolve best cost for the given password
	 * 
	 * @param  string $password
	 * @param  int $cost The number of cost
	 * @param  int $expected The total number of expected time to find the best cost
	 * 
	 * @return int
	 */
	public function resolveCost($password, $cost = 10, $expected = 0.05)
	{
		do {

			$cost++;
			$start = microtime(true);
			$this->make($password, PASSWORD_BCRYPT, array('cost' => $cost));
			$end = microtime(true);

		} while(($end - $start) < $expected);

		return $cost;
	}
}
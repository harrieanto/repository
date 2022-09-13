<?php
namespace Repository\Component\Contracts\Hashing;

/**
 * Hash Interface.
 * 
 * @package	 \Repository\Component\Contracts\Hashing
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface HashInterface
{
	/**
	 * Make passowrd hash
	 * 
	 * @param  string  $password
	 * @param  string  $type Password type
	 * @param  array $optionals
	 * 
	 * @return string Hashed password
	 */
	public function make($password, $type = PASSWORD_BCRYPT, $optionals = array());

	/**
	 * Determine if the given password match with the given hash
	 * 
	 * @param  string $password
	 * @param  string $hash Hashed password
	 * 
	 * @return boolean
	 */
	public function isValid($password, $hash);

	/**
	 * Determine if the given password is expired
	 * 
	 * @param  string $password
	 * 
	 * @return boolean
	 */	
	public function isExpired($password);
}
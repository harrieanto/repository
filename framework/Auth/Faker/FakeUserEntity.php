<?php
namespace Repository\component\Auth\Faker;

/**
 * The Fake Concrete Impelementation of User Entity Interface.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class FakeUserEntity
{
	private $username;
	private $password;

	public function __construct($attributes = array())
	{
		if (array_key_exists('password', $attributes)) {
			$this->password = $attributes['password'];
		}	

		if (array_key_exists('username', $attributes)) {
			$this->username = $attributes['username'];
		}	
	}
	
	public function getUsername()
	{
		if ($this->username === null) {
			return false;
		}
		
		return $this->username;
	}
	
	public function  getHashedPassword()
	{
		if ($this->password === null) {
			return false;
		}
		
		return $this->password;
	}
}
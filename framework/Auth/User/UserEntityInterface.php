<?php
namespace Repository\Component\Auth\User;

/**
 * The Minimal User Entity Requirement Must be Implement.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface UserEntityInterface
{
	public function getHashedPassword();
}
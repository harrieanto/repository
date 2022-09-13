<?php
namespace Repository\Component\Contracts\Auth;

/**
 * Auth Interface.
 * 
 * @package	 \Repository\Component\Contracts\Auth
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface AuthInterface
{
	/**
	 * Authenticate the given credential payload
	 * 
	 * @param \Repository\Component\Contracts\Auth\CredentialInterface
	 * @param string $error The error message bag
	 * 
	 * @return bool false When authentication failed, true otherwise
	 */
	public function authenticate(CredentialInterface $credential, &$error);
}
<?php
namespace Repository\Component\Auth;

/**
 * Credential Types.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class CredentialTypes
{
	/** Access token type **/
	const ACCESS_TOKEN = 'ACCESS_TOKEN';
	
	/** Refresh token type **/
	const REFRESH_TOKEN = 'REFRESH_TOKEN';
	
	const SIGNED_TOKEN_VERIFICATION = 'SIGNED_TOKEN_VERIFICATION';
	
	const USERNAME_PASSWORD = 'USERNAME_PASSWORD';
}
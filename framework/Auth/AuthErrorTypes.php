<?php
namespace Repository\Component\Auth;

/**
 * Auth Error Types.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class AuthErrorTypes
{
	/** Invalid username error type **/
	const INVALID_USERNAME = 'INVALID_USERNAME';

	/** Username not found error type **/	
	const USERNAME_NOT_FOUND = 'USERNAME_NOT_FOUND';

	/** Invalid password error type **/
	const INVALID_PASSWORD = 'INVALID_PASSWORD';

	/** Credential missing error type **/	
	const CREDENTIAL_IS_MISSING = 'CREDENTIAL_IS_MISSING';

	/** SIgned jwt token not found error type **/	
	const SIGNED_JWT_TOKEN_NOT_FOUND = 'SIGNED_JWT_TOKEN_NOT_FOUND';
}
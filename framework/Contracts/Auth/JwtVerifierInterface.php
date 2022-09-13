<?php
namespace Repository\Component\Contracts\Auth;

use Repository\Component\Auth\Jwt\JwtToken;
use Repository\Component\Auth\Jwt\Verification\Context;

/**
 * JWT Verifier Interface.
 * 
 * @package	 \Repository\Component\Contracts\Auth
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface JwtVerifierInterface
{
	/**
	 * Verify the given signed jwt attributes with the attribute rules
	 * 
	 * @param \Repository\Component\Auth\Jwt\JwtToken $jwt
	 * @param string $error Referenced error message
	 *  
	 * @return True when passed and false otherwise
	 */
	public function verify(JwtToken $jwt, &$error);
}
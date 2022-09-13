<?php
namespace Repository\Component\Auth\Jwt\Repository;

/**
 * The Minimal Jwt Repository Requirement Must be Implement.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface JwtRepository
{
	/**
	 * Determine if the given signed jwt is available or not
	 * 
	 * @param string $signedJwt The signed/persisted jwt token
	 * 
	 * @return bool true when available, false otherwise
	 */
	public function has($signedJwt);

	/**
	 * Get signed jwt informations by the given signed jwt
	 * 
	 * @param string $signedJwt
	 * 
	 * @return null|bool|array
	 */	
	public function getBySignedJwt(string $signedJwt);
}
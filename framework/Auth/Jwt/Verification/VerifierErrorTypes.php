<?php
namespace Repository\Component\Auth\Jwt\Verification;

/**
 * The Error Types of Verifier.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class VerifierErrorTypes
{
	/** The invalid issuer message **/
	const INVALID_ISSUER = 'INVALID_ISSUER';

	/** The invalid audience message **/
	const INVALID_AUDIENCE = 'INVALID_AUDIENCE';

	/** The invalid subject message **/
	const INVALID_SUBJECT = 'INVALID_SUBJECT';

	/** The not found signed signature message **/
	const MISS_MATCH_SIGNATURE = 'MISS_MATCH_SIGNATURE';

	/** The invalid signature message **/	
	const INVALID_SIGNATURE = 'INVALID_SIGNATURE';

	/** The expired message **/
	const TOKEN_EXPIRED = 'TOKEN_EXPIRED';

	/** The invalid message when the token used before the allowed time **/
	const TOKEN_USED_BEFORE_THE_TIME = 'TOKEN_USED_BEFORE_THE_TIME';
}
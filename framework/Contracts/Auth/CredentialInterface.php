<?php
namespace Repository\Component\Contracts\Auth;

/**
 * Auth Credential Interface.
 * 
 * @package	 \Repository\Component\Contracts\Auth
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface CredentialInterface
{
	public function getType();
	
	public function getValue($name);
	
	public function getValues();
}
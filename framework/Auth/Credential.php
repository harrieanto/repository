<?php
namespace Repository\Component\Auth;

use Repository\Component\Contracts\Auth\CredentialInterface;

/**
 * Credential Value Object.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Credential implements CredentialInterface
{
	/**
	 * The credential type
	 * @var string $type
	 */
	private $type = 'jwt';

	/**
	 * The credential values
	 * @var string|array $values
	 */	
	private $values = array();

	/**
	 * @param string $type
	 * @param string|array $values
	 */
	public function __construct($type, $values)
	{
		$this->type = $type;
		$this->values = $values;
	}

	/**
	 * @return string The credential type
	 */	
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get credential value by the given key
	 * 
	 * @return string
	 */	
	public function getValue($name)
	{
		if (array_key_exists($name, $this->values)) {
			return $this->values[$name];
		}
	}

	/**
	 * Get credential values
	 * 
	 * @return array|string
	 */		
	public function getValues()
	{
		return $this->values;
	}
}
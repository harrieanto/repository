<?php
namespace Repository\Component\Environment;

use Repository\Component\Environment\Exception\EnvironmentException;

/**
 * Environemt Identity Validator.
 *
 * @package	  \Repository\Component\Environment
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class EnvironmentValidator
{
	/**
	 * Environment identifiers
	 * 
	 * @var array
	 */
	protected $identifiers = array();

	/**
	 * Environment Loader
	 * @var \Repository\Component\Environment\Environment $environment
	 */
	protected $environment;

	/**
	 * @param array  $identifiers
	 * @param \Repository\Component\Environment\Environment $environment
	 */
	public function __construct(array $identifiers, Environment $env)
	{
		$this->environment = $env;
		$this->identifiers = $identifiers;

		$this->identifier(function($value) {
				return $value !== null;
		}, 'Is missing.');
	}

	/**
	 * Check whether identifier is formed as integer or not
	 * 
	 * @return string
	 */
	public function isInteger()
	{
		return $this->identifier(
			function($val){
				return ctype_digit($val);
			}, 'Is not an integer.'
		);
	}

	/**
	 * Check whether given identifier valid or not
	 * 
	 * @param  string $identifier
	 * 
	 * @return string
	 */
	public function allowedIdentifier($identifier)
	{
		return $this->identifier(
			function($value) use ($identifier){
				return in_array($value, $identifier);
			}, 'Is not allowed identifier.'
		);
	}

	/**
	 * Check identifier
	 * 
	 * @param  Closure $callback
	 * @param  string $message
	 * 
	 * @return string
	 */
	protected function identifier($callback, $message = 'Failed')
	{
		//check if first parameter callback or not
		if (!is_callable($callback)) {
			throw new EnvironmentException(
				'first parameter must be declared as callback.');
		}

		foreach ($this->identifiers as $offset) {
			$identifiers = $this->loader->getEnvironmentVariable($offset);

			if (!call_user_func($callback, $identifiers)) {
				return $offset." ".$message;
			}
		}
	}
}
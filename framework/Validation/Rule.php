<?php
namespace Repository\Component\Validation;

use Closure;
use Repository\Component\Collection\Collection;

/**
 * The Rule of Validation.
 * 
 * @package	  \Repository\Component\Validation
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Rule
{
	/**
	 * Validation rule container
	 * @var array $validationRules
	 */
	protected static $validationRules = array();

	/**
	 * Deafult alert messages
	 * @var array $alerts
	 */
	public $alerts = array(
		'required' => 'Field that expected is required and empty given', 
		'letter' => 'Field that expected is an letter format and another given', 
		'alpha' => 'Field that expected is an alphabetical format and another given', 
		'alnum' => 'Field that expected is an alpha-numeric format and another given', 
		'email' => 'Field that expected is an email format and another given', 
		'url' => 'Field that expected is an url format and another given', 
		'number' => 'Field that expected is an number format and another given', 
		'birthday' => 'Field that expected is an birthday format and another given', 
		'year' => 'Field that expected is an year format and another given', 
		'day' => 'Field that expected is an day format and another given', 
		'any' => 'Field that expected is any but this field can\'t empty too', 
		'minimal' => 'Length of %s have to equal with %d', 
		'maximal' => 'Length of %s have to more than equal with %d', 
		'minmax' => 'Length of %s have to equal with %d and more than equal %d', 
		'equal'	=> 'Character that you have typed of %s miss match with %s', 
		'not-allowed-media-type' => 'File media type not allowed', 
		'json' => 'Invalid json data', 
		'datetime' => 'Invalid datetime format', 
		'string' => 'String value expected', 
		'file_exist' => 'File not found', 
		'bool' => 'Boolean value expected', 
		'integer' => 'Integer value expected', 
		'numeric' => 'Nummeric value expected', 
		'between' => 'The data %s must be either %s', 
		'domain' => 'Invalid domain name', 
		//File error handling  
		UPLOAD_ERR_OK => 'There is no error, the file uploaded with success', 
		UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini', 
		UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 
		UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded', 
		UPLOAD_ERR_NO_FILE => 'No file was uploaded'
	);

	/**
	 * Default validation rules
	 * @var array $vdefaultRules
	 */
	public $defaultRules = array(
		'required' => 'required', 

		'letter' => '~^[\pL+\pLl+\pN\s\.\,-_\?\!+]+$~ixu', 
		
		'alpha' => '~^[a-zA-Z\s+]+$~i', 

		'alnum' => '~^[0-9a-zA-Z\s+]+$~i', 

		'email' => 	'~^[\pL\pLl\pN].+@[a-z+]+\.[a-z]{1,5}$~ixu',
		
		'number' => '~^[\pN+]+$~ixu',

		'birthday' => '~^[\pN]{4}\-[\pN]{2}\-[\pN]{2}$~ixu',

		'year' => '~^[\pN{4}]$~ixu',

		'day' => '~^[\pN{2}]$~ixu',

		'any' => '~^.+$~ixu'
	);

	/**
	 * Initialize validation rule container
	 * 
	 * @return \Repository\Component\Validation\Rule
	 */
	public static function make()
	{
		self::$validationRules = array();
		
		return new static;
	}

	/**
	 * Set validation rule to the initialized container
	 * 
	 * @param \Closure $expectedRuleCallback Validation rule callback
	 * 
	 * @return void
	 */
	public function setRule(Closure $expectedRuleCallback)
	{
		$build = Collection::make((array) $expectedRuleCallback($this));
		
		self::$validationRules = $build;
	}

	/**
	 * Determine if the given rule found in default rules
	 * 
	 * @param string $rule The key of rule
	 * 
	 * @return bool
	 */
	public function hasRule($rule)
	{
		if (self::$validationRules->has($rule))
			return true;
		return false;
	}

	/**
	 * Get validation rule by the given key
	 * 
	 * @param string $key The key of rule
	 * 
	 * @return string
	 */
	public function getRule($key)
	{
		if ($this->hasRule($key))
			return self::$validationRules->get($key);
	}

	/**
	 * Get default rules from the rule container
	 * 
	 * @return array
	 */
	public function getDefaultRules()
	{
		return $this->defaultRules;
	}

	/**
	 * Get custom rules from initialized container
	 * 
	 * @return array
	 */
	public function getRules()
	{
		$rules = self::$validationRules->all();
		
		return $rules;
	}

	/**
	 * Set alert message to the container by the given callback
	 * 
	 * @param \Closure $alertCallback
	 * 
	 * @return void
	 */
	public function setAlertMessage(Closure $alertCallback)
	{
		$alert = (array) $alertCallback($this);
		
		$this->alerts = array_merge($this->alerts, $alert);
	}

	/**
	 * Get alert messages from the container
	 * 
	 * @return array
	 */	
	public function getAlertMessages()
	{
		$alerts = $this->alerts;
		return $alerts;
	}

	/**
	 * Get alert message by the given key
	 * 
	 * @return string
	 */	
	public function getAlertMessage($key)
	{
		$alert = $this->alerts[$key];
		return $alert;
	}
}
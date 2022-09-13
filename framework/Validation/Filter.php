<?php
namespace Repository\Component\Validation;

/**
 * Filter.
 * 
 * @package	  \Repository\Component\Validation
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Filter
{
	/**
	 * Payload to filter
	 *
	 * @var string|int $payload
	 */
	private static $payload;

	/**
	 * 
	 * Initialize payload
	 * 
	 * @param  integer  $payload
	 * 
	 * @return \Repository\Component\Validation
	 * 
	 */
	public static function make($payload)
	{
		self::$payload = $payload;
		
		return new static;
	}

	/**
	 * 
	 * Determine if the given value is equal
	 * 
	 * @param  integer  $payload The value to check
	 * @param  integer  $expected The expected value
	 * 
	 * @return boolean
	 * 
	 */
	public static function isEqual($payload, $expected)
	{
		if ($payload === $expected) return true;
		
		return false;
	}

	/**
	 * 
	 * Determine if the given value is equal with the given expected number
	 * 
	 * @param  integer  $expected The expected value
	 * 
	 * @return boolean
	 * 
	 */
	public function isEqualWith($expected)
	{
		if (self::$payload === $expected) return true;
		
		return false;
	}

	/**
	 * Determine if the given value is null
	 * 
	 * @param  integer  $expected The expected value
	 * 
	 * @return boolean
	 */
	public static function isNull($payload = null)
	{
		if ($payload !== null) {
			if ($payload === null) return true;
			
			return false;
		}

		if (static::$payload === null) return true;
		
		return false;
	}

	/**
	 * Get length of the string payload
	 * 
	 * @return int
	 */	
	public function length($payload = null)
	{
		$length =  mb_strlen($payload ? $payload : self::$payload);
		
		return $length;
	}

	/**
	 * Determine if the payload less than the given expected number
	 * 
	 * @param int $expected
	 * 
	 * @return int
	 */	
	public function isLessThan(int $expected)
	{
		$payload = self::$payload;

		if (!is_int(self::$payload)) $payload = $this->length($payload);
		
		return ($payload < $expected)?true:false;
	}

	/**
	 * Determine if the payload less than equal the given expected number
	 * 
	 * @param int $expected
	 * 
	 * @return bool
	 */	
	public function isLessThanEqual(int $expected)
	{
		$payload = self::$payload;

		if (!is_int(self::$payload)) $payload = $this->length($payload);

		return ($payload <= $expected)?true:false;
	}

	/**
	 * Determine if the payload greater than the given expected number
	 * 
	 * @param int $expected
	 * 
	 * @return bool
	 */	
	public function isGreaterThan(int $expected)
	{
		$payload = self::$payload;

		if (!is_int(self::$payload)) $payload = $this->length($payload);
		
		return ($payload > $expected)?true:false;
	}

	/**
	 * Determine if the payload greater than equal the given expected number
	 * 
	 * @param int $expected
	 * 
	 * @return bool
	 */	
	public function isGreaterThanEqual(int $expected)
	{
		$payload = self::$payload;

		if (!is_int(self::$payload)) $payload = $this->length($payload);
		
		return ($payload >= $expected)?true:false;
	}

	/**
	 * Convert string to lower case
	 * 
	 * @return string
	 */	
	public function toLower()
	{	
		$lower =  mb_strtolower(self::$payload);
		
		return $lower;
	}

	/**
	 * Convert string to upper case
	 * 
	 * @return string
	 */	
	public function toUpper()
	{	
		$upper =  mb_strtoupper(self::$payload);
		
		return $upper;
	}

	/**
	 * Resolve custom type pattern to the regular expression of the given payload
	 * 
	 * @param  array  $patterns
	 * @param  string|null  $payload
	 * 
	 * @return boolean
	 */
	public function resolveTypePattern(array $patterns, $payload = null)
	{
		$payload = self::$payload;

		if (!self::isNull($payload)) $payload = $payload;

		foreach($patterns as $type => $pattern) {
			if (preg_match("@\:{1}$type@u", $payload, $matches)) {
				$type = "/".$matches[0]."/";
				self::$payload = preg_replace($type, $pattern, $payload);
			}
		}
	}

	/**
	 * Resolve alpha type to the regular expression
	 * 
	 * @return string Resolved payload
	 */
	public function alpha()
	{
		$payload = self::$payload;

		self::resolveTypePattern(
			array('alpha' => '([A-Za-z_-]+)+'), 
			$payload
		);
		
		return self::$payload;
	}

	/**
	 * Resolve alhpa numeric type to the regular expression by the given payload
	 * 
	 * @return string Resolved payload
	 */
	public function alnum()
	{
		$payload = self::$payload;

		self::resolveTypePattern(
			array('alnum' => '([0-9A-Za-z_-]+)'), 
			$payload
		);
		
		return self::$payload;
	}

	/**
	 * Resolve digit type to the regular expression
	 * 
	 * @return string Resolved payload
	 */
	public function digit()
	{
		$payload = self::$payload;

		self::resolveTypePattern(
			array('digit' => '([0-9]+)+'), 
			$payload
		);
		
		return self::$payload;
	}

	/**
	 * Resolve lower type to the regular expression
	 * 
	 * @return string Resolved payload
	 */
	public function lower()
	{
		$payload = self::$payload;

		self::resolveTypePattern(
			array('lower' => '([a-z]+)+'), 
			$payload
		);
		
		return self::$payload;
	}

	/**
	 * Resolve upper type to the regular expression
	 * 
	 * @return string Resolved payload
	 */
	public function upper()
	{
		$payload = self::$payload;

		self::resolveTypePattern(
			array('upper' => '([A-Z]+)+'), 
			$payload
		);
		
		return self::$payload;
	}

	/**
	 * Resolve any type to the regular expression

	 * @return string Resolved payload
	 */
	public function any()
	{
		$payload = self::$payload;

		self::resolveTypePattern(
			array('any' => '([^/].+)+'), 
			$payload
		);
		
		return self::$payload;
	}

	/**
	 * Resolve custom type to the regular expression
	 *  
	 * @param array $expecteds Paired expected type pattern
	 * 
	 * @return string Resolved payload
	 */
	public function setType(array $expecteds)
	{
		$payload = self::$payload;

		self::resolveTypePattern($expecteds, $payload);
		
		return self::$payload;
	}

	/**
	 * Get last filtered payload
	 * 
	 * @return string Resolved payload
	 */
	public function getPayload()
	{
		return $this->__toString();
	}

	/**
	 * Call resolved Payload when object is printed
	 * 
	 * @return string Resolved payload
	 */	
	public function __toString()
	{
		return self::$payload;
	}
}
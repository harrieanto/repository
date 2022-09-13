<?php
namespace Repository\Component\Mail;

/**
 * Mail Charset Converter.
 *
 * @package	  \Repository\Component\Mail
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class CharsetConverter
{
	/**
	 * Callback function to use for character set conversion to UTF8.
	 * @var array The callback helper definition for utf-8 convertion
	 */
	private static $method = array(__CLASS__, 'convertToUTF8Iconv');

	/**
	 * Sets the callback function used for character set conversion to UTF-8.
	 *
	 * Call this method before doing mail parsing if you need a special way
	 * of converting the character set to UTF-8.
	 *
	 * @param callback $method
	 * 
	 * @return void
	 */
	public static function setConvertMethod($method)
	{
		self::$method = $method;
	}

	/**
	 * Converts the $text with the charset $originalCharset to UTF-8.
	 *
	 * It calls the function specified by using the static method
	 * setConvertMethod(). By default it calls convertToUTF8Iconv() defined
	 * in this class.
	 *
	 * @param string $text
	 * @param string $originalCharset
	 * 
	 * @return string
	 */
	public static function convertToUTF8($text, $originalCharset)
	{
		return call_user_func(self::$method, $text, $originalCharset);
	}

	/**
	 * Converts the $text with the charset $originalCharset to UTF-8.
	 *
	 * In case $originalCharset is 'unknown-8bit' or 'x-user-defined' then
	 * it is assumed to be 'latin1' (ISO-8859-1).
	 *
	 * @param string $text
	 * @param string $originalCharset
	 * 
	 * @return string
	 */
	public static function convertToUTF8Iconv($text, $originalCharset)
	{
		if ($originalCharset === 'unknown-8bit' || $originalCharset === 'x-user-defined') {
			$originalCharset = "latin1";
		}
		
		return iconv($originalCharset, 'utf-8', $text);
	}

	/**
	 * Encode the given value to the Utf8 encoding
	 *
	 * @param string $value The value to encode.
	 *
	 * @return string
	 */
	public function encodeUtf8($value)
	{
		$value = trim($value);
		
		if (preg_match('/(\s)/', $value)) {
			return $this->encodeUtf8Words($value);
		}
		
		return $this->encodeUtf8Word($value);
	}

	/**
	 * Encode the given value to the Utf8 encoding
	 *
	 * @param string $value The word to encode.
	 *
	 * @return string
	 */
	public function encodeUtf8Word($value)
	{
		return sprintf('=?UTF-8?B?%s?=', base64_encode($value));
	}

	/**
	 * 
	 * Encode the given values to the Utf8 encoding
	 *
	 * @param string $value The words to encode.
	 *
	 * @return string
	 * 
	 */
	public function encodeUtf8Words($value)
	{
		$words = explode(' ', $value);
		$encoded = array();
		
		foreach ($words as $word) {
			$encoded[] = $this->encodeUtf8Word($word);
		}
		
		return implode($this->encodeUtf8Word(' '), $encoded);
	}
}
<?php
namespace Repository\Component\Support;

use RuntimeException;

/**
 * String Manipulation.
 * 
 * @package	  \Repository\Component\Support
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Str
{
    /**
     * The cache of studly-cased words.
     * @var array
     */
    protected static $studlyCache   = array();

    /**
     * The cache of camel-cased words
     * @var array
     */
    protected static $camelCache    = array();

    /**
     * The cache of snake-cased words
     * @var array
     */
    protected static $snakeCache    = array();

    /**
     * Convert a value to studly caps case
     * 
     * @param  string  $value
     * 
     * @return string
     */
    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * Convert a value to camel case
     * 
     * @param string $value
     * 
     * @return string
     */
    public static function camel($value)
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    /**
     * Convert a string to snake case
     * 
     * @param string $value
     * @param string $delimeter
     * 
     * @return string
     */
    public static function snake($value, $delimeter = '_')
    {
        $key = $value;
        if (isset(static::$snakeCache[$key][$delimeter])) {
            return static::$snakeCache[$key][$delimeter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace("/\s+/u", "", $value);
            $value = static::lower(preg_replace("/(/)(?=[A-Z])/u", "$1".$delimeter, $value));
        }

        return static::$snakeCache[$key][$delimeter] = $value;
    }

    /**
     * Return the length of the given string
     * 
     * @param string $value
     * 
     * @return string
     */
    public static function length($value)
    {
        return mb_strlen($value);
    }

    /**
     * Find position of the first occurence of a string in a string
     * 
     * @param string $haystack
     * @param string $needle
     * 
     * @return string
     */
    public static function pos($haystack, $needle)
    {
        return mb_strpos($haystack, $needle);
    }

    /**
     * Limit the number of the given string
     * 
     * @param string  $value
     * @param integer $limit
     * @param string  $end
     * 
     * @return string
     */
    public static function limit($value, $limit = 100, $end = '...')
    {
        if(mb_strlen($value)< $limit) return $end;
        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')).$end;
    }

	/**
     * Limit the number of word space in a string
	 * 
	 * @param  string  $value
	 * @param  integer $length 
	 * @param  string  $end 
	 * 
	 * @return string
	 */
	public static function limitBySpace($value, $length = 50, $end = '...')
	{
		$values = explode(' ', $value);
		
		if (count($values) < $length) {
            $length = count($values)-1;
		}

		$values = array_map(function($length) use ($values) {

			$value = $values[$length].' ';
			return $value;

			}, range(0, $length)
		);
		
		$value = implode(' ', $values).$end;
		
		return $value;
	}

	/**
     * Repeat the number of given string
     * 
	 * @param  string      $value
	 * @param  int|integer $loop number of times repaeat the given string
	 * 
	 * @return string
	 */
	public static function repeat($value = '&nbsp;', int $loop = 1)
	{
		return str_repeat($value, $loop);
	}

    /**
     * Determine if the given string contains a given substring
     * 
     * @param string $haystack
     * @param string|array $needles
     * 
     * @return boolean
     */
    public static function contains($haystack, $needles)
    {
        foreach((array) $needles as $needle) {
            if(($needle !== '') && (mb_strpos($haystack, $needle) !== false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse a Class@method style callback into class and method
     * 
     * @param string $callback
     * @param string $default
     * 
     * @return array
     */
    public static function parseCallback($callback, $default)
    {
        return static::contains($callback, '@')?explode('@', $callback, 2):array($callback, $default);
    }

    /**
     * Generate random alpha-numeric string
     * 
     * @param int $length
     * 
     * @return string
     */
    public static function random($length = 16)
    {
        $alnum = bin2hex(self::randomBytes($length));
        
        return $alnum;
    }

    /**
     * Generate random bytes
     * 
     * @param int $length
     * 
     * @return binary
     * 
     * @throws \RuntimeException
     */
    public static function randomBytes($length = 16)
    {
        if (PHP_MAJOR_VERSION >= 7) {
            $bytes = random_bytes($length);
        } elseif(function_exists('openssl_random_psudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length, $strong);
            if(($bytes === false) && ($strong === false)) {
                throw new RuntimeException("Unable to generate random string");
            }
        } else {
            throw new RuntimeException("OpenSSL extension is required for PHP 5");
        }
        return $bytes;
    }

    /**
     * Generate basic and quick random string
     * Shouldn't considered for cryptography, etc
     * 
     * @param int $length
     * 
     * @return string
     */
    public function basicRandom($length = 16)
    {
        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()_-+?><~';
		return substr( str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param  string  $value
     * @return string
     */
    public static function ascii($value)
    {
        return \Patchwork\Utf8::toAscii($value);
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * 
     * @return string
     */
    public static function slug($title, $separator = '-')
    {
        //$title = static::ascii($title);

        // Convert all dashes/underscores into separator
        $flip = $separator == '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Convert the given string to lower-case
     * 
     * @param string $value
     * 
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value);
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param  string  $value
     * 
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value);
    }

    /**
     * Convert the given string to title case
     * 
     * @param string $value
     * 
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param  string  $string
     * @param  int  $start
     * @param  int|null  $length
     * 
     * @return string
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Make a string's first character uppercase.
     *
     * @param  string  $string
     * 
     * @return string
     */
    public static function ucfirst($string)
    {
        return static::upper(static::substr($string, 0, 1)) .static::substr($string, 1);
    }
}
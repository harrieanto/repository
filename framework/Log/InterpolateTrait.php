<?php
namespace Repository\Component\Log;

use Throwable;

/**
 * Interpolate message and context of the logger
 *
 * @package	  \Repository\Component\Log
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
trait InterpolateTrait
{
	/**
	 * Interpolate message placeholder with the given context
	 * 
	 * @param string $message
	 * @param string|array $context
	 * 
	 * @return string
	 */	
	public function interpolate(string $message, array $context = array())
	{
		$items = array();
		
		foreach ($context as $index => $item) {
			if (is_string($item))
				$items["{$index}"] = $item;
		}
		
		return strtr($message, $items);
	}

	/**
	 * Read log by the given path
	 * 
	 * @param string $path
	 * 
	 * @return array
	 */	
	public function read($path)
	{
		$contents = array();
		$content = file_get_contents($path);

		foreach (explode("\n\n", $content) as $value) {
			$contents[] = json_decode($value, true);
		}
		
		return $contents;
	}
}
<?php
namespace Repository\Component\Console\Responses\Formatter;

/**
 * Background.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Background
{
	/** The default background **/
	const DEFAULT = self::BLACK;
	/** The black background **/
	const BLACK = array('black' => 40);
	/** The red background **/
	const RED = array('red' => 41);
	/** The green background **/
	const GREEN = array('green' => 42);
	/** The yellow background **/
	const YELLOW = array('yellow' => 43);
	/** The blue background **/
	const BLUE = array('blue' => 44);
	/** The magenta background **/
	const MAGENTA = array('magenta' => 45);
	/** The cyan background **/
	const CYAN = array('cyan' => 46);
	/** The light gray background **/
	const LIGHT_GRAY = array('light_gray' => 47);
	/** The background options **/
	const OPTIONS = array(
		Background::BLACK, 
		Background::RED, 
		Background::GREEN, 
		Background::YELLOW, 
		Background::BLUE, 
		Background::MAGENTA, 
		Background::CYAN, 
		Background::LIGHT_GRAY, 
		TextFormattingTypes::RESET
	);
}
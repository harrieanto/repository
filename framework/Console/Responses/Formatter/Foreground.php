<?php
namespace Repository\Component\Console\Responses\Formatter;

/**
 * Foreground/Text Color.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Foreground
{
	/** The default foreground **/
	const DEFAULT = self::BLACK;
	/** The black foreground **/
	const BLACK = array('black' => '30');
	/** The dark foreground **/
	const DARK = array('dark' => '0;30');
	/** The dark gray foreground **/
	const DARK_GRAY = array('dark_gray' => '1;30');
	/** The blue foreground **/
	const BLUE = array('blue' =>'0;34');
	/** The light blue foreground **/
	const LIGHT_BLUE = array('light_blue' => '1;34');
	/** The green foreground **/
	const GREEN = array('green' => '0;32');
	/** The light green foreground **/
	const LIGHT_GREEN = array('light_green' => '1;32');
	/** The cyan foreground **/
	const CYAN = array('cyan' => '0;36');
	/** The light cyan foreground **/
	const LIGHT_CYAN = array('light_cyan' => '1;36');
	/** The red foreground **/
	const RED = array('red' => '0;31');
	/** The light red foreground **/
	const LIGHT_RED = array('light_red' => '1;31');
	/** The purple foreground **/
	const PURPLE = array('purple' => '0;35');
	/** The light purple foreground **/
	const LIGHT_PURPLE = array('light_purple' => '1;35');
	/** The light gray foreground **/
	const LIGHT_GRAY = array('light_gray' => '0;37');
	/** The brown foreground **/
	const BROWN = array('brown' => '0;33');
	/** The white foreground **/
	const WHITE = array('white' => '1;37');
	/** The yellow foreground **/
	const YELLOW = array('yellow' => '1;33');
	/** Foreground options **/
	const OPTIONS = array(
		Foreground::BLACK, 
		Foreground::DARK, 
		Foreground::DARK_GRAY, 
		Foreground::BLUE, 
		Foreground::LIGHT_BLUE, 
		Foreground::GREEN, 
		Foreground::LIGHT_GREEN, 
		Foreground::CYAN, 
		Foreground::LIGHT_CYAN, 
		Foreground::RED, 
		Foreground::LIGHT_RED, 
		Foreground::PURPLE, 
		Foreground::LIGHT_PURPLE, 
		Foreground::LIGHT_GRAY, 
		Foreground::BROWN, 
		Foreground::WHITE, 
		Foreground::YELLOW, 
		TextFormattingTypes::RESET, 
	);
}
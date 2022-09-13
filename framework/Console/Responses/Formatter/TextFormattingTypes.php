<?php
namespace Repository\Component\Console\Responses\Formatter;

/**
 * ANSI Text Formatting Types.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class TextFormattingTypes
{
	/** Reset text formatting **/
	const RESET = array('reset', 0);
	/** The bold text formatting **/
	const BOLD = array('bold', 1);
	/** The italic text formatting **/
	const ITALIC = array('italic', 3);
	/** The underline text formatting **/
	const UNDERLINE = array('underline', 4);
	/** The blink text formatting **/
	const BLINK = array('blink', 5);
	/** The reverse text formatting **/
	const REVERSE = array('reverse', 7);
	/** The hidden text formatting **/
	const HIDDEN = array('hidden', 8);
}
<?php
namespace Repository\Component\Console\Responses\Formatter;

/**
 * Text Column Alignment Types for Table
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class TextColumnTypes
{
	/** The left alignment @var string**/
	const LEFT = 'left';
	/** The right alignment @var string**/
	const RIGHT = 'right';
	/** The center alignment @var string**/
	const CENTER = 'center';
	/** The default text charset @var string**/
	const CHARSET = 'UTF-8';
	/** The default value when the tablew row is empty @var string**/
	const EMPTY_ENTRY = 'NULL';
	/** The position text alignment options @var array**/
	const POSITIONS = array(
		TextColumnTypes::LEFT, 
		TextColumnTypes::RIGHT, 
		TextColumnTypes::CENTER
	);
}
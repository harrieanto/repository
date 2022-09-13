<?php
namespace Repository\Component\Contracts\Console;

/**
 * Console Table Formatter Interface.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface TableFormatterInterface
{
	/*
	 * Render parsed data as table to the actual resource (STDOUT)
	 * 
	 * @return void
	 */	
	public function renderTable();

	/*
	 * Get parsed field/header for the table
	 * 
	 * @return string
	 */
	public function getParsedFieldColumn();

	/*
	 * Get parsed row body for the table
	 * 
	 * @return string
	 */
	public function getParsedRowColumn();
}
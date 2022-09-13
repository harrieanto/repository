<?php
namespace Repository\Component\Contracts\Console;

/**
 * Progress Bar Handler.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface ProgressBarTypeInterface
{
	/**
	 * Create progress bar line string
	 * 
	 * @param int $progress
	 * @param int $unfinished
	 * 
	 * @return string
	 */
	public function createProgressBar(int $progress, int $unfinished): string;

	/**
	 * Get type of current progress bar
	 * 
	 * @return string
	 */	
	public function getType(): string;
}
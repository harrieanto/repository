<?php
namespace Repository\Component\Console\ProgressBars\Types;

use Repository\Component\Console\ProgressBars\ProgressBar;
use Repository\Component\Contracts\Console\ProgressBarTypeInterface;
use Repository\Component\Console\ProgressBars\Types\ProgressBarTypes;

/**
 * Spinner Progress Bar.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Spinner implements ProgressBarTypeInterface
{
	/**
	 * The progress bar instance
	 * @var \Repository\Component\Console\ProgressBars\ProgressBar $progressBar
	 */
	protected $progressBar;

	/**
	 * The spinner characters
	 * @var array $spinners
	 */
	private $spinners = array('|', '/', '-', '\\', '|', '/', '-', '\\');

	/**
	 * @param \Repository\Component\Console\ProgressBars\ProgressBar $progressBar
	 */	
	public function __construct(ProgressBar $progressBar)
	{
		$this->progressBar = $progressBar;
	}

	/**
	 * Create progress bar line string
	 * 
	 * @param int $progress
	 * @param int $unfinished
	 * 
	 * @return string
	 */
	public function createProgressBar(int $progress, int $unfinished): string
	{
		$emptyCharacter = $this->progressBar->getEmptyCharacter();
		$lineBarCharacter = $this->progressBar->getBarCharacter();
		$spinnerLineString = str_repeat($lineBarCharacter, $progress);
		$spinnerLineString.= $this->appendBarSpinner($progress);
		$spinnerLineString.= str_repeat($emptyCharacter, $unfinished);
		
		return $spinnerLineString;
	}

	/**
	 * Append spinner by the given current progress
	 * 
	 * @param int $currentProgress
	 * 
	 * @return string
	 */
	public function appendBarSpinner($currentProgress)
	{
		$position = $currentProgress % count($this->spinners);
		
		if (isset($this->spinners[$position])) {
			if ($this->progressBar->getPercentage() >= 100) {
				return $this->progressBar->getBarCharacter();
			}
			return $this->spinners[$position];
		}
	}

	/**
	 * Get type of current progress bar
	 * 
	 * @return string
	 */	
	public function getType(): string
	{
		return ProgressBarTypes::SPINNER;
	}
}
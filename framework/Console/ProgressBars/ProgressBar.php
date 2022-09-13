<?php
namespace Repository\Component\Console\ProgressBars;

use Repository\Component\Console\ProgressBars\Types\Spinner;
use Repository\Component\Contracts\Console\ResponseInterface;
use Repository\Component\Contracts\Console\ProgressBarTypeInterface;
use Repository\Component\Console\ProgressBars\Types\ProgressBarTypes;

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
class ProgressBar
{
	/** @var int The default time how long progress bar increased **/
	const USLEEP_TIME = 25000;

	/** @var int The default value how much minimal width of screen perceived **/
	const MINIMAL_WIDTH = 10;

	/** @var int The default length how much allocated empty space reserved from maximal screen width **/
	const DEFAULT_ALLOCATED_SPACE = 7;

	/** @var string The default bar character **/
	const DEFAULT_BAR_CHARACTER = '-';
	
	/** @var string The default empty character **/
	const DEFAULT_EMPTY_CHARACTER = ' ';
	
	/** @var string The default progress character **/
	const DEFAULT_PROGRESS_CHARACTER = '>';
	
	/**
	 * Start progress bar unit
	 * @var int $start
	 */
	private $start;

	/**
	 * Determine whether or not progres bar has started
	 * @var bool $started
	 */
	private $started = false;

	/**
	 * The specific progress bar message interpolation this is
	 * @var string $message
	 */	
	private $message = '';

	/**
	 * Progress bar format in string
	 * @var string $stringBarFormat
	 */
	private $stringBarFormat = '';

	/**
	 * The string bar formats container
	 * @var array $stringBarFormats
	 */
	private $stringBarFormats = array();

	/**
	 * Definition progress bar formats
	 * @var array $barFormats
	 */
	private $barFormats = array();

	/**
	 * The conserve space for another use like display percentage, filename, etc...
	 * @var int $allocatedSpace
	 */
	private $allocatedSpace;

	/**
	 * The width of bar indicator
	 * @var int $barWidth
	 */
	private $barWidth;

	/**
	 * How much finished progress indicator
	 * @var int $finishedProgress
	 */
	private $finishedProgress;

	/**
	 * How much remaining/unfinished progress indicator
	 * @var int $unfinishedProgress
	 */
	private $unfinishedProgress;

	/**
	 * Registered progress bar types container
	 * @var array $registeredProgressBars
	 */
	private $registeredProgressBars = array();

	/**
	 * Setup progress bar parameter
	 * @param \Repository\Component\Contracts\Console\ResponseInterface $response
	 */
	public function __construct(ResponseInterface $response)
	{
		$this->response = $response;
		$this->setMessage('');
		$this->registerDefaultProgressBar();
		$this->setBarCharacter(self::DEFAULT_BAR_CHARACTER);
		$this->setEmptyCharacter(self::DEFAULT_EMPTY_CHARACTER);
		$this->setProgressCharacter(self::DEFAULT_PROGRESS_CHARACTER);
		$this->setBarFormat(ProgressBarTypes::DEFAULT_BAR);
		$allocatedSpace = self::DEFAULT_ALLOCATED_SPACE;
		$this->setMaximalWidth();
		$this->allocateEmptySpace($allocatedSpace);
		$this->resolveBarWidth();
	}

	/**
	 * Start progress bar unit
	 * 
	 * @return void
	 */
	public function start(int $total)
	{
		$this->start = 0;
		$this->started = true;
		$this->setTotalProcess($total);
		$this->response->getCursor()->save();
	}

	/**
	 * Determine if the progress bar is running
	 * 
	 * @return bool
	 */	
	public function isStarted()
	{
		return (bool) $this->started;
	}

	/**
	 * Handle output response when process is done.
	 * 
	 * @return void
	 */
	public function finish()
	{
		if ($this->isStarted()) {
			if ($this->getPercentage() >= 100) {
				$this->response->comment('Done.', true);
			}
		}
	}

	/**
	 * Increase progress bar as long as the maximal loop reached.
	 * 
	 * @param int $step The how much progress bar step wanted
	 * 
	 * @return void
	 */
	public function advance(int $step = 100)
	{
		if (!$this->isStarted()) return;
		
		$progress = round($this->start/$this->total*$step);
		$finished = round($progress / $step * $this->barWidth);
		$remaining = $this->barWidth - $finished;

		$this->setFinishedProgress($finished);
		$this->setUnfinishedProgress($remaining);
		$this->resolveRegisteredProgressBars();

		$this->setDefinitionBarFormat("%percent%", function ($bar, $response) {
			return $this->getPercentage() . '%';
		});
		
		$this->response->getCursor()->restore();
		$this->response->info($this->getBarFormatLineString(), true);

		$this->start++;

		usleep(self::USLEEP_TIME);
	}

	/**
	 * Allocate space for another use
	 * 
	 * @param int the width of conserve space wanted
	 * 
	 * @param void
	 */	
	public function allocateEmptySpace(int $width)
	{
		$this->allocatedSpace = strlen($this->getPercentage())+$width;
	}

	/**
	 * Resolve the width of bar by defined allocated space
	 * So we can perceived progress bar output properly
	 * 
	 * @return void
	 */	
	public function resolveBarWidth()
	{
		$width = $this->maximalWidth - $this->allocatedSpace;
		$this->barWidth = $width;
	}

	/**
	 * Get the width of bar by defined allocated space
	 * 
	 * @return int
	 */	
	public function getBarWidth()
	{
		return (int) $this->barWidth;
	}

	/**
	 * Set how much current finished progress bar percentage
	 * 
	 * @param float|int $amount
	 * 
	 * @return void
	 */
	public function setFinishedProgress($amount)
	{
		$this->finishedProgress = $amount;
	}

	/**
	 * Set how much current remaining/unfinished progress bar percentage
	 * 
	 * @param float|int $amount
	 * 
	 * @return void
	 */	
	public function setUnfinishedProgress($amount)
	{
		$this->unfinishedProgress = $amount;
	}

	/**
	 * Get how much current finished progress bar percentage
	 * 
	 * @return int|float
	 */
	public function getFinishedProgress()
	{
		return $this->finishedProgress;
	}

	/**
	 * Get how much current remaining/unfinished progress bar percentage
	 * 
	 * @return int|float
	 */		
	public function getUnfinishedProgress()
	{
		return $this->unfinishedProgress;
	}

	/**
	 * Register default progress bar types
	 * 
	 * @return void
	 */	
	public function registerDefaultProgressBar()
	{
		$progressBars = array(Spinner::class);

		foreach ($progressBars as $bar) {
			$bar = new $bar($this);
			$this->register($bar->getType(), $bar);
		}
	}

	/**
	 * Register progress bar types to the progress bar container
	 * 
	 * @param string $type
	 * @param \Repository\Component\Contracts\Console\ProgressBarTypeInterface $progressBar
	 * 
	 * @return void
	 */
	public function register(string $type, ProgressBarTypeInterface $progressBar)
	{
		$this->registeredProgressBars[$type]['instance'] = $progressBar;
	}

	/**
	 * Resolve registered progress bar instance onto progress bar line string
	 * 
	 * @return void
	 */
	private function resolveRegisteredProgressBars()
	{
		$progress = $this->getFinishedProgress();
		$unfinished = $this->getUnfinishedProgress();

		foreach ($this->registeredProgressBars as $type => $bars) {
			foreach ($bars as $bar) {
				if ($bar instanceof ProgressBarTypeInterface) {
					$bar = $bar->createProgressBar($progress, $unfinished);
					$this->registeredProgressBars[$type]['resolved'] = $bar;
				}
			}
		}

		$this->setDefinitionBarFormat("%bar%", function ($bar, $response) {
			$barFormats = $this->registeredProgressBars;

			//First we will deterime if the bar format exists one
			//in the bar container. If exist we can use it as bar output
			if (isset($barFormats[$this->getBarFormat()])) {
				$barFormats = $barFormats[$this->getBarFormat()];
				return  $barFormats['resolved'];
			}

			//If the bar format doesn't exist int the registered bar container
			//We will give default bar
			return $barFormats[ProgressBarTypes::DEFAULT_BAR]['resolved'];
		});
	}

	/**
	 * Set maximal width of terminal screen
	 * 
	 * @param null|int $width
	 * 
	 * @return void
	 */
	public function setMaximalWidth(int $width = null)
	{
		$this->maximalWidth = $this->response->getScreenWidth();
		
		if (is_int($width) && $width > self::MINIMAL_WIDTH) {
			$this->maximalWidth = $width;
		}
	}

	/**
	 * Set bar format definition in line string
	 * 
	 * @param string $format The format type of progress bar definition
	 * 
	 * @return void
	 */	
	public function setBarFormat($format = ProgressBarTypes::DEFAULT_BAR)
	{
		$this->stringBarFormat = $format;
		
		//Here we'll setup default progress bar format
		switch ($format) {
			case ProgressBarTypes::DEFAULT_BAR:
				$this->setBarFormatLineString(
					ProgressBarTypes::DEFAULT_BAR, 
					"%message%:[%bar%]:%percent%"
				); break;
		}
	}

	/**
	 * Get bar format definition in line string
	 * 
	 * @return string
	 */
	public function getBarFormat()
	{
		return (string) $this->stringBarFormat;
	}

	/**
	 * Define output progress bar format in line string.
	 * 
	 * @param string $type
	 * @param string $format
	 * 
	 * @return void
	 */
	public function setBarFormatLineString(string $type, string $format)
	{
		$this->stringBarFormats[$type] = $format;
	}

	/**
	 * Get defined output progress bar format in line string
	 * 
	 * @return string
	 */
	public function getBarFormatLineString()
	{
		$stringBarFormat = $this->stringBarFormats[$this->stringBarFormat];
		
		if ($stringBarFormat !== '') {
			$format = explode(':', $stringBarFormat);
			$format = $this->interpolate(implode('', $format), $this->barFormats);

			return $format;
		}
	}

	/**
	 * Define output progress bar format for interpolation
	 * 
	 * @param string $format The format type of progress bar definition
	 * 
	 * @return void
	 */		
	public function setDefinitionBarFormat($definition, \Closure $callback)
	{
		$this->barFormats[$definition] = (string) $callback($this, $this->response);
	}

	/**
	 * Get defined output progress bar format for interpolation
	 * 
	 * @param string $key
	 * 
	 * @return string
	 */	
	public function getDefinitionBarFormat($key)
	{
		if (isset($this->barFormats[$key])) {
			return (string) $this->barFormats[$key];
		}
	}

	/**
	 * Get progress bar percentage
	 * 
	 * @return int|float
	 */	
	public function getPercentage()
	{
		if ($this->isStarted()) {
			return (int) floor($this->start/$this->total*100);
		}
	}

	/**
	 * Set total data should be handle
	 * 
	 * @param int $total The total data should be handle
	 * 
	 * @return void
	 */
	private function setTotalProcess($total): void
	{
		$this->total = $total;
	}

	/**
	 * Define specific message for output interpolation.
	 * 
	 * @param string $message The progress bar message this is
	 * 
	 * @return void
	 */
	public function setMessage(string $message): void
	{
		$this->message .= $message;

		$callback = function ($bar, $response) {
			return $this->message;
		};

		$this->setDefinitionBarFormat('%message%', $callback);
	}

	/**
	 * Set empty character.
	 * 
	 * @param string $character
	 * 
	 * @return \Repository\Component\Console\ProgressBars\ProgressBar
	 */	
	public function setEmptyCharacter(string $character)
	{
		$this->emptyCharacter = $character;
		
		return $this;
	}

	/**
	 * Get empty character.
	 * 
	 * @return string
	 */	
	public function getEmptyCharacter(): string
	{
		return $this->emptyCharacter;
	}

	/**
	 * Set progress character.
	 * 
	 * @param string $character
	 * 
	 * @return \Repository\Component\Console\ProgressBars\ProgressBar
	 */	
	public function setProgressCharacter(string $character)
	{
		$this->progressCharacter = $character;
		
		return $this;
	}

	/**
	 * Get progress character.
	 * 
	 * @param string $character
	 * 
	 * @return string
	 */	
	public function getProgressCharacter(): string
	{
		return $this->progressCharacter;
	}

	/**
	 * Set bar character.
	 * 
	 * @param string $character
	 * 
	 * @return \Repository\Component\Console\ProgressBars\ProgressBar
	 */	
	public function setBarCharacter(string $character)
	{
		$this->barCharacter = $character;
		
		return $this;
	}

	/**
	 * Get bar character.
	 * 
	 * 
	 * @return string
	 */	
	public function getBarCharacter(): string
	{
		return $this->barCharacter;
	}

	/**
	 * Interpolate message placeholder with the given context
	 * 
	 * @param string $message
	 * @param string|array $contexts
	 * 
	 * @return string
	 */	
	public function interpolate(string $message, array $contexts = array())
	{
		$items = array();
		
		foreach ($contexts as $index => $item) {
			if (is_string($item))
				$items[$index] = $item;
		}
		
		return strtr($message, $items);
	}
}
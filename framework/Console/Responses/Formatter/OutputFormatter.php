<?php
namespace Repository\Component\Console\Responses\Formatter;

use InvalidArgumentException;
use Repository\Component\Contracts\Console\OutputFormatterInterface;

/**
 * ANSI Output Formatting.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class OutputFormatter implements OutputFormatterInterface
{
	/**
	 * The passed foreground used for formatting
	 * @var null|array $foreground
	 */
	private $foreground;

	/**
	 * The passed background used for formatting
	 * @var null|array $background
	 */
	private $background;

	/**
	 * The list of default foregrounds
	 * @var array $foregrounds
	 */
	private $foregrounds = array();

	/**
	 * The list of default backgrounds
	 * @var array $backgrounds
	 */
	private $backgrounds = array();

	/**
	 * The list of foreground/text formatting
	 * @var array $textFormattings
	 */
	private $textFormattings = array();

	/**
	 * Setup default background and foreground to the list
	 */
	public function __construct()
	{
		$this->resolveDefaultForeground();
		$this->resolveDefaultBackground();
	}

	/**
	 * Add paired key-value foreground to the list
	 * 
	 * @param string $key The name of foreground
	 * @param string $value The value of the given foreground name
	 * 
	 * @return \Repository\Component\Contracts\Console\OutputFormatterInterface
	 */	
	public function addForeground(string $key, string $value)
	{
		$this->foregrounds[$key] = $value;
		
		return $this;
	}

	/**
	 * Add paired key-value background to the list
	 * 
	 * @param string $key The name of foreground
	 * @param string $value The value of the given foreground name
	 * 
	 * @return \Repository\Component\Contracts\Console\OutputFormatterInterface
	 */	
	public function addBackground($key, $value)
	{
		$this->backgrounds[$key] = $value;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Console\OutputFormatterInterface::setStringFormatting()
	 */	
	public function setStringFormatting(string &$message, \Closure $formatter = null)
	{
		$foreground = $this->parseToConsoleArgument(Foreground::DEFAULT);
		$formatting = $foreground . $message . "\033[0m";

		if ($formatter === null) {
			$message = $formatting;
			return $message;
		}

		$formatting = '';
		$formatter = $formatter($this);
		
		if (!$formatter instanceof $this) $formatter = $this;

		if ($formatter->getTextColor() !== null) {
			$formatting .= $this->parseToConsoleArgument($formatter->getTextColor());
		}

		if ($formatter->getBackgroundColor() !== null) {
			$formatting .= $this->parseToConsoleArgument($formatter->getBackgroundColor());
		}

		if (!empty($formatter->getTextFormattings())) {
			foreach ($formatter->getTextFormattings() as $formatting) {
				$formatting .= $this->parseToConsoleArgument($formatting);
			}
		}
		
		$message = $formatting . $message . "\033[0m";
		
		$this->setTextColor(TextFormattingTypes::RESET);

		return $message;
	}

	/**
	 * Transform the given style type into ANSI format
	 * 
	 * @param array $styles The formatting styles
	 * @param string $option
	 * 
	 * @return string
	 */	
	public function parseToConsoleArgument(array $styles, $option = 'm')
	{
		$styles = array_values($styles);

		return sprintf("\033[%s%s", array_shift($styles), $option);
	}

	/**
	 * Add text formatting
	 * 
	 * @param array $format The text format
	 * 
	 * @return \Repository\Component\Contracts\Console\OutputFormatterInterface
	 */	
	public function addTextFormatting(array $format)
	{
		$this->textFormattings[] = $format;

		return $this;
	}

	/**
	 * Get text formattings
	 * 
	 * @return string
	 */	
	public function getTextFormattings()
	{
		return $this->textFormattings;
	}

	/**
	 * Get passed background volor
	 * 
	 * @return array
	 */	
	public function getBackgroundColor()
	{
		return $this->background;
	}

	/**
	 * Sett background color
	 * 
	 * @param array $color
	 * 
	 * @return \Repository\Component\Contracts\Console\OutputFormatterInterface
	 */	
	public function setBackgroundColor(array $color)
	{
		foreach ($color as $textColor => $ascii) {
			if (!array_key_exists($textColor, $this->backgrounds) && !in_array($ascii, $this->backgrounds)) {
				throw new InvalidArgumentException("The given background color is not supported.");
			}
		}

		$this->background = $color;
		return $this;
	}

	/**
	 * Get passed text/foreground color
	 * 
	 * @return string
	 */	
	public function getTextColor()
	{
		return $this->foreground;
	}

	/**
	 * Set text/foreground color
	 * 
	 * @param array $color
	 * 
	 * @return \Repository\Component\Contracts\Console\OutputFormatterInterface
	 */	
	public function setTextColor(array $color)
	{
		foreach ($color as $textColor => $ascii) {
			if (!array_key_exists($textColor, $this->foregrounds) && !in_array($ascii, $this->foregrounds)) {
				throw new InvalidArgumentException("The given background color is not supported.");
			}
		}

		$this->foreground = $color;
		return $this;
	}

	/**
	 * Resolve default foreground color
	 * 
	 * @return void
	 */
	private function resolveDefaultForeground(): void
	{
		foreach (Foreground::OPTIONS as $options) {
			foreach ($options as $textColor => $ascii) {
				$this->addForeground($textColor, $ascii);
			}
		}
	}

	/**
	 * Resolve default background color
	 * 
	 * @return void
	 */
	private function resolveDefaultBackground(): void
	{
		foreach (Background::OPTIONS as $options) {
			foreach ($options as $textColor => $ascii) {
				$this->addBackground($textColor, $ascii);
			}
		}
	}
}
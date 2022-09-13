<?php
namespace Repository\Component\Contracts\Console;

/**
 * The Prompt Question Handler.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface QuestionInterface
{
	/**
	 * Parse answer in the appropriate output
	 * The return value maybe different depends on the question context
	 * Here you can just see plain return value. 
	 * Let another child class override as their question context
	 * 
	 * @param string $answer
	 * 
	 * @return mixed
	 */
	public function parseAnswer($answer);

	/**
	 * Get defined default answer of the mentioned question
	 * 
	 * @return string
	 */	
	public function getDefaultAnswer();

	/**
	 * Get defined question
	 * 
	 * @return string
	 */		
	public function getQuestion();
}
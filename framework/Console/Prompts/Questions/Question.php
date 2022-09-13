<?php
namespace Repository\Component\Console\Prompts\Questions;

use Repository\Component\Contracts\Console\QuestionInterface;

/**
 * The Default Console Question Handler.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Question implements QuestionInterface
{
	/**
	 * The question
	 * @var string $question
	 */
	protected $question;

	/**
	 * The default answer of question
	 * @var null|bool|string $defaultAnswer
	 */
	protected $defaultAnswer;

	/**
	 * Setup question and default answer
	 * 
	 * @param string $question
	 * @param null|string|bool $defaultAnswer
	 */
	public function __construct(string $question, $defaultAnswer = null)
	{
		$this->question = $question;
		$this->defaultAnswer = $defaultAnswer;
	}

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
	public function parseAnswer($answer)
	{
		return $answer;
	}

	/**
	 * Get defined default answer of the mentioned question
	 * 
	 * @return string
	 */	
	public function getDefaultAnswer()
	{
		return $this->defaultAnswer;
	}

	/**
	 * Get defined question
	 * 
	 * @return string
	 */		
	public function getQuestion()
	{
		return $this->question;
	}
}
<?php
namespace Repository\Component\Console\Prompts;

use InvalidArgumentException;
use Repository\Component\Contracts\Console\ResponseInterface;
use Repository\Component\Contracts\Console\QuestionInterface;
use Repository\Component\Console\Prompts\Questions\Confirmation;
use Repository\Component\Console\Prompts\Questions\MultipleChoice;
use Repository\Component\Console\Responses\Formatter\TextFormatting;

/**
 * Console Prompt Question.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Prompt
{
	/** The default answer line @var string **/
	const ANSWER_PROMPT_LINE = '> ';

	/**
	 * The input stream to fetch to
	 * @var resource $inputStream
	 */
	private $inputStream;

	/**
	 * The text formatter instance
	 * @var \Repository\Component\Console\Responses\Formatter\TextFormatting $formatter
	 */	
	private $formatter;

	/**
	 * @param resource $inputStream
	 */
	public function __construct(ResponseInterface $response, $stream = STDIN)
	{
		$this->setInputStream($stream);
		$this->response = $response;
		$this->formatter = new TextFormatting();
	}

	/**
	 * Question Handler
	 * 
	 * @param \Repository\Component\Contracts\Console\QuestionInterface $question
	 * @param \Repository\Component\Contracts\Console\ResponseInterface $response
	 * 
	 * @return string|array The answer inputed by user
	 */	
	public function handle(QuestionInterface $question, ResponseInterface $response)
	{
		$response->info($question->getQuestion(), true);
		
		if ($question instanceof MultipleChoice) {
			$response->info('', true);
			foreach ($question->getChoices() as $index => $choice) {
				$choice = $this->formatter->parseTwoSideColumn($index, $choice);
				$response->warning($choice, true);
			}
		}
		
		$response->info('', true);
		$response->info(self::ANSWER_PROMPT_LINE);
		
		$answer = trim(fgets($this->inputStream, 4096));

		if ($answer === '') {
			$answer = $question->getDefaultAnswer();
		}

		$answer = $question->parseAnswer($answer);

		return $answer;
	}

	/**
	 * Ask Question
	 * 
	 * @param string $question
	 * @param array $choices
	 * @param null|string|bool $defaultAnswer
	 * 
	 * @return string|array The answer inputed by user
	 */
	public function ask(string $question, array $choices = array(), $defaultAnswer = null)
	{
		$question = new MultipleChoice($question, $choices, $defaultAnswer);

		return $this->handle($question, $this->response);
	}

	/**
	 * Confirmation Question
	 * 
	 * @param string $question
	 * @param null|string|bool $defaultAnswer
	 * 
	 * @return bool
	 */
	public function confirm(string $question, $defaultAnswer = null)
	{
		$question = new Confirmation($question, $defaultAnswer);

		return $this->handle($question, $this->response);
	}

	/**
	 * Set input stream resource
	 * 
	 * @param resource $stream
	 * 
	 * @throws \InvalidArgumentException
	 * 
	 * @return void
	 */
	public function setInputStream($stream): void
	{
		if (!is_resource($stream)) {
			throw new InvalidArgumentException("The given input stream should be resource.");
		}
		
		$this->inputStream = $stream;
	}

	/**
	 * Get input stream resource
	 * 
	 * @return resource
	 */		
	public function getInputStream()
	{
		return $this->inputStream;
	}
}
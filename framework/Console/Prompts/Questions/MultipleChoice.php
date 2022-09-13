<?php
namespace Repository\Component\Console\Prompts\Questions;

/**
 * The Console Question in The Multiple Choice Handler.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class MultipleChoice extends Question
{
	/**
	 * The answer choices of the metioned question
	 * @var array $choices
	 */
	private $choices = array();

	/**
	 * The inputed answer given by the user of the metioned question
	 * @var string $answer
	 */
	private $answer;

	/**
	 * Setup question and default answer
	 * 
	 * @param string $question
	 * @param array $choices
	 * @param null|string|bool $defaultAnswer
	 */
	public function __construct(string $question, array $choices, $defaultAnswer = null)
	{
		parent::__construct($question, $defaultAnswer);
		$this->choices = $choices;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Prompts\Questions\Question::parseAnswer()
	 */
	public function parseAnswer($answer)
	{
		$hasMultipleAnswer = false;

		if (is_string($answer)) {
			$this->setAnswerLineString($answer);
		}
		
		$answer = explode(',', $answer);
		
		if (count($answer) > 1) $hasMultipleAnswer = true;
		
		$answer = $this->getSelectedAnswer($answer);
		
		if ($hasMultipleAnswer === false) {
			if (empty($answer)) {
				return $this->getDefaultAnswer();
			}
			
			return $answer[0];
		}
		
		return $answer;
	}

	/**
	 * Set answer in the line string format
	 * 
	 * @param string $answer
	 * 
	 * @return void
	 */	
	public function setAnswerLineString(string $answer)
	{
		$this->answer = $answer;
	}

	/**
	 * Get answer in the line string format
	 * 
	 * @return string
	 */	
	public function getAnswerLineString()
	{
		return $this->answer;
	}

	/**
	 * Get defined choices of the mentioned question
	 * 
	 * @return array
	 */		
	public function getChoices()
	{
		return $this->choices;
	}

	/**
	 * Get selected answer given by user
	 * 
	 * @param array $answers
	 * 
	 * @return array
	 */
	public function getSelectedAnswer(array $answers): array
	{
		$collectedAnswers = array();

		foreach ($answers as $answer) {
			$answer = trim($answer);
			$isTheAnswerWithinKey = array_key_exists($answer, $this->choices);
			
			if (in_array($answer, $this->choices) || $isTheAnswerWithinKey) {
				if ($isTheAnswerWithinKey) {
					$collectedAnswers[$answer] = $this->choices[$answer];
				} else {
					$choices = array_flip($this->choices);
					$collectedAnswers[$choices[$answer]] = $answer;
				}
			}
		}
		
		ksort($collectedAnswers);
		
		return $collectedAnswers;
	}
}
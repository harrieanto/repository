<?php
namespace Repository\Component\Console\Prompts\Questions;

/**
 * The Console Confirmation Question Handler.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Confirmation extends Question
{
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Prompts\Questions\Question
	 */
	public function __construct(string $question, $defaultAnswer = null)
	{
		parent::__construct($question, $defaultAnswer);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Console\Prompts\Questions\Question::parseAnswer()
	 */
	public function parseAnswer($answer)
	{
		if (is_bool($answer)) return $answer;

		return mb_strtolower($answer[0]) === 'y';
	}
}
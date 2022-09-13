<?php
namespace Repository\Component\View\Compiler;

/**
 * Replace unresolved content based on resolved content specifications.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Maker
{
	/**
	 * Translator instance
	 * 
	 * @var \Repository\Component\View\Compiler\Translator $translator
	 */
	protected $translator;

	/**
	 * @param \Repository\Component\View\Compiler\Translator $translator
	 */	
	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * Replace content groups specification by the given replacements
	 * 
	 * @param string $groups The list of type block group name
	 * @param string|array $replacements Replacement lists
	 * 
	 * @return void
	 */	
	public function make(array $groups, array $replacements)
	{
		$unresolved = $this->translator->getContent();

		$unresolved = str_replace($groups, $replacements, $unresolved);

		$this->translator->make($unresolved);
	}

	/**
	 * Replace content group specification by the given replacement
	 * 
	 * @param string $group The type of block group name
	 * @param string|array $replacements Replacement lists
	 * 
	 * @return void
	 */	
	public function makeBy(string $group, $replacements)
	{
		$groups = $this->translator->getContentsBy($group);
		
		$unresolved = $this->translator->getContent();

		$unresolved = str_replace($groups['matches'], $replacements, $unresolved);

		$this->translator->make($unresolved);
	}
}
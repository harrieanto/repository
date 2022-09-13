<?php
namespace Repository\Component\View\Compiler;

use Closure;
use Repository\Component\View\Compiler\Exception\TranslatorException;

/**
 * Resolve Stateement Block Specification.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Conditional
{
	/**
	 * Translator instance
	 * 
	 * @var \Repository\Component\View\Compiler\Translator $translator
	 */
	protected $translator;
	
	/**
	 * Maker instance
	 * 
	 * @var \Repository\Component\Compiler\Maker $maker
	 */	
	protected $maker;

	/**
	 * @param \Repository\Component\View\Compiler\Translator $translator
	 * @param \Repository\Component\Compiler\Maker $maker
	 */	
	public function __construct(Translator $translator, Maker $maker)
	{
		$this->translator = $translator;
		$this->maker = $maker;
	}

	/**
	 * Resolve if block specification
	 * 
	 * @return void
	 */	
	public function resolveIf()
	{
		$operator = "\$\d\w\-\>\=\!\(\)\[\]\"\'\.\s";

		$pattern = "/
			(@if\()
			([$operator]+)
			(\))
			([\s\W\w\d\D]+?)
			(@endif)/x";

		$this->translator->setPattern($pattern);
		$this->resolve(Compiler::IF, 'if', 'endif;');
	}

	/**
	 * Resolve loop block specification
	 * 
	 * @return void
	 */	
	public function resolveLoop()
	{
		$operator = "\$\d\w\-\>\=\!\(\)\[\]\"\'\.\s";
		
		$pattern = "/
			(@loop\()
			([$operator]+\;[$operator]+\;[$operator]+)
			(\))
			([\s\W\w\d\D]+?)
			(@endloop)/x";

		$this->translator->setPattern($pattern);
		$this->resolve(Compiler::LOOP, 'for', 'endfor;');
	}

	/**
	 * Resolve foreach block specification
	 * 
	 * @return void
	 */	
	public function resolveForeach()
	{
		$operator = "\$\d\w\-\>\=\!\(\)\[\]\"\'\.\s";
		
		$pattern = "/
			(@foreach\()
			([$operator]+as[$operator]+[(\=\>)]?[\$\w\s]+?)
			(\))
			([\s\W\w\d\D]+?)
			(@endforeach)/x";

		$this->translator->setPattern($pattern);
		$this->resolve(Compiler::FOREACH, 'foreach', 'endforeach;');
	}

	/**
	 * Resolve block specification
	 * 
	 * @param $group Block group name
	 * @param $open Block open specification
	 * @param $close Block close specification
	 * 
	 * @return void
	 */	
	public function resolve($group, $open, $close)
	{
		$alias = substr($group, 1, strlen($group));

		$this->resolveMissingNesting(
			$group, 
			"@$alias(", 
			"@end$alias"
		);
		
		$pattern = $this->translator->getPattern();

		$this->translator->resolveContentBy($pattern, $group);

		$this->translator->setGroupName($group);

		if(isset($this->translator->items[$group])) {

			$conditionals = $this->translator->getContentsBy($group);

			foreach($conditionals['content'] as $index => $conditional) {

				$matches = $conditionals['open'][0].
					$conditional.
					$conditionals['close'][0];
					
				$conditional = "$open(".$conditional."):";

				$conditional = $this->translator
					->resolvePhpContainer($conditional);

				$this->translator->items[$group]['matches'][$index] = $matches;

				$conditionals['content'][$index] = $conditional;
			}

			$this->maker->makeBy($group, $conditionals['content']);

			$close = $this->translator->resolvePhpContainer($close);
			
			$breaks = array();
			
			for($i=0;$i<count($conditionals['stop']);$i++) {
				$breaks[] = $close;
			}

			$this->maker->make($conditionals['stop'], $breaks);
		}

	}

	/**
	 * Resolve missing nesting in the block specification
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */	
	public function resolveMissingNesting(
		$group, 
		$open, 
		$close, 
		Closure $exception = null)
	{
		$content = $this->translator->getContent();
		$open = $this->getNestedPositions($content, $open);
		$close = $this->getNestedPositions($content, $close);
		
		if ($open && $open !== null) {
			//Here we will surprised \Countable fatal error
			//when nesting get weird
			$open = count((array) $open);
			$close = count((array) $close);
			
			//Whenever open nesting not match with close nesting
			//There nothing we can do except throw an exception
			if ($open !== $close) {

				if ($exception !== null) return $exception();
			
				$file = $this->translator->getMainFile();
				
				$ex = "Missing nesting. Conditional statement [$group] was failed. [$file]";

				throw new TranslatorException($ex);
			}
		}
	}

	/**
	 * Get nested positions of the given block/character specification
	 * 
	 * @param string $content
	 * @param string $character Block specification
	 * 
	 * @return array
	 */
	public function getNestedPositions($content, $character)
	{
		if (!strpos($content, $character)) {
			return;
		}

		$positions[0] = strpos($content, $character);

		while(!(strpos(
			$content, 
			$character, 
			$positions[count($positions)-1]+1)) === false) {
			
			$positions[] = strpos(
				$content, 
				$character, 
				$positions[count($positions)-1]+1
			);
		}
		
		return $positions;
	}
}
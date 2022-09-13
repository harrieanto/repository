<?php
namespace Repository\Component\View\Compiler;

use Psr\Http\Message\StreamInterface;

/**
 * Compiler Factory.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class CompilerFactory
{	
	/**
	 * Filesystem instance
	 * 
	 * @var \Psr\Message\StreamInterface $fs
	 */
	protected $fs;

	/**
	 * Translator instance
	 * 
	 * @var \Repository\Component\View\Compiler\Translator $translator
	 */
	protected $translator;

	/**
	 * @param \Psr\Message\StreamInterface $fs
	 */	
	public function __construct(StreamInterface $fs)
	{
		$this->fs = $fs;
		$this->translator = new Translator($fs);
	}

	/**
	 * Set content and resolve the given content template specification
	 * 
	 * @param string $content
	 * @param string $basepath Target basepath
	 * @param string $cacheBasepath Cache target basepath
	 * for compiled/resolved content specification
	 * 
	 * @return \Respository\Component\View\CompilerFactory
	 */
	public function make(string $content, $basepath, $cacheBasepath)
	{
		$this->translator->setBasepath($basepath);
		$this->translator->make($content);
		$this->translator->resolve();
		$this->save($content, $cacheBasepath);

		return $this;
	}

	/**
	 * Persist compiled content to the specific target storage
	 * 
	 * @param string $target Target path name
	 * @param string $basepath The target cache basepath
	 * 
	 * @return void
	 */
	public function save($target, $basepath)
	{
		$writer = new Writer($this->fs, $this->translator);
		$writer->save($target, $basepath);
	}

	/**
	 * 
	 * Handle Translator method dynamically
	 * 
	 * @param string $methodName
	 * @param mixed $parameters
	 * 
	 * @return mixed
	 */	
	public function __call($methodName, $parameters)
	{
		$classes = array($this->translator, $methodName);
		return call_user_func_array($classes, $parameters);
	}
}
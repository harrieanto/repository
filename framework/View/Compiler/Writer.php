<?php
namespace Repository\Component\View\Compiler;

use Closure;
use Psr\Http\Message\StreamInterface;

/**
 * Persisting resolved content specifications to the specific cache file.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Writer
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
	 * Content list
	 * 
	 * @var array $parsedContents
	 */
	protected $parsedContents = array();

	/**
	 * Use classes
	 * 
	 * @var array $useClasses
	 */
	protected $useClasses = array();

	/**
	 * @param \Psr\Http\Message\StreamInterface $fs
	 * @param \Repository\Component\Compiler\Translator $translator
	 */	
	public function __construct(
		StreamInterface $fs, 
		Translator $translator)
	{
		$this->fs = $fs;
		$this->translator = $translator;
	}

	/**
	 * Persist compiled content to the specific target storage
	 * 
	 * @param string $target Target path name
	 * @param string $basepath Target basepath
	 * for compiled/resolved content specification
	 * 
	 * @return void
	 */	
	public function save($target, $basepath)
	{
		if($this->fs->isFile($target)) {
			$target = $this->fs->file(
				array($basepath => md5($target)), 
				Compiler::DIR_POINTING
			);

			$this->translator->setCompiledPath($target);

			$this->compose($target);
		}
	}

	/**
	 * Resolve cache directory
	 * 
	 * @param string $cacheFileTarget Target path file
	 * 
	 * @return void
	 */	
	private function resolveCacheDirectory($cacheFileTarget)
	{
		$directory = $this->fs->paths($cacheFileTarget)['dirname'];

		if (readdir(opendir($directory))) {
			if (!is_writable($directory)) {
				throw new \RuntimeException(
					"Directory [$directory] not writable"
				);
			}
			
			if (!$this->fs->isDirectory($directory)) {
				$this->fs->createDir($directory,  0777, true);
			}
		}
	}

	/**
	 * Write resolved content specification to the specifictarget storage
	 * 
	 * @param string $cacheFileTarget Target path file
	 * 
	 * @return void
	 */	
	public function compose($cacheFileTarget)
	{
		$fileBeforeCache = $this->translator->getMainFile();
		$layout = $this->translator->getLayoutTarget();

		$this->resolveCacheDirectory($cacheFileTarget);

		if  (file_exists($cacheFileTarget) && file_exists($fileBeforeCache) && 
			(time()-filemtime($fileBeforeCache) > Compiler::EXPIRED_TIME &&
			 time()-filemtime($layout) > Compiler::EXPIRED_TIME) && 
			(time()-filemtime($cacheFileTarget) > Compiler::EXPIRED_TIME)) {
				return;
		}
		
		$this->dump($fileBeforeCache, $cacheFileTarget);
	}

	/**
	 * Populate resolved content specification to the specific target storage
	 * 
	 * @param string $fileBeforeCache Main path file
	 * @param string $cacheFileTarget Target path file
	 * 
	 * @return void
	 */	
	private function dump($fileBeforeCache, $cacheFileTarget)
	{
		$useClass = $this->getUseClasses();

		if (!is_null($useClass) && count($useClass) > 0) {
			foreach ($this->getUseClasses() as $key => $value) {				
				$this->setContent("use", function($writer) use ($value) {
					return $this->translator->resolvePhpContainer("use $value;");
				});
			}
		}

		$this->setContent("parsed", function($container) {
			return $this->translator->getContent();
		});
		
		$content = implode(PHP_EOL, $this->getParsedContents());
		//Determine if file before cache exist and file after compiled is missed
		if (file_exists($fileBeforeCache) && !file_exists($cacheFileTarget))
			$this->fs->make($cacheFileTarget, 'w+');
		
		//Determine if file before cache exist and file after compiled is match too
		//also, expired cache time less than expired time boundary
		if (file_exists($fileBeforeCache) && 
			file_exists($cacheFileTarget)  && 
		   (time()-filemtime($fileBeforeCache ) || 
		   	time()-filemtime($cacheFileTarget)) >= Compiler::EXPIRED_TIME) {

			$this->fs->putContent($cacheFileTarget, $content);
			return true;

		}
	}

	/**
	 * Resolve initial content to the compiled content
	 * 
	 * @return void
	 */	
	private function resolveInitialContent()
	{
		$this->setContent("initial",  function($writer) {
			$date = $writer->getCurrentTime();
			return <<<EOT
\n<?php

/**
 *
 * Author : harrieanto31@yahoo.com | Web:http://bandd.web.id
 * -----------------------------------------------------------------
 * Created : $date
 * This view has been produced by RTE Template Engine
 * automaticly
 * As Repository PHP Framework Components
 * -----------------------------------------------------------------
 * Github : https://github.com/harrieanto
 *
 **/

\nrequire_once __DIR__.'/../../../vendor/autoload.php';\n\n?>
EOT;
		});
	}

	/**
	 * Get current time
	 * 
	 * @return string
	 */	
	public function getCurrentTime()
	{
		return date("d/M/Y h:i:s A");
	}

	/**
	 * Set use class to the compiled content
	 * So we could do more stuff with helper class in the view layer
	 * 
	 * @param string $key
	 * @param string $useClass `use Namespace\To\Specific\Class`
	 * 
	 * @return \Respository\Component\Compiler\Writer
	 */	
	public function setUseClass($key, $useClass)
	{
		$this->useClasses[$key] = $useClass;

		return $this;
	}

	/**
	 * Get use class
	 * 
	 * @return array
	 */	
	public function getUseClasses()
	{
		return $this->useClasses;
	}

	/**
	 * Set additional content to the parsed content container
	 * 
	 * @param $key
	 * @param \Closure $container
	 * 
	 * @return void
	 */	
	public function setContent($key, Closure $container)
	{
		return $this->parsedContents[$key] = $container($this);
	}

	/**
	 * Get aditional content by key
	 * 
	 * @param $key
	 * 
	 * @return string
	 */	
	public function getParsedContent($key)
	{
		return $this->parsedContents[$key];
	}

	/**
	 * Get aditional contents
	 * 
	 * @param $key
	 * 
	 * @return string
	 */	
	public function getParsedContents()
	{
		return $this->parsedContents;
	}
}
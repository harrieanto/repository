<?php
namespace Repository\Component\View;

use Psr\Http\Message\StreamInterface;
use Repository\Component\Support\Str;
use Repository\Component\Config\Config;
use Repository\Component\Collection\Collection;
use Repository\Component\View\Compiler\Compiler;
use Repository\Component\View\Exception\ViewException;
use Repository\Component\Contracts\View\ViewInterface;
use Repository\Component\View\Compiler\CompilerFactory;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * View Handler.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class View implements ViewInterface
{
	/**
	 * View path name
	 * @var string|array $target
	 */
	private $targets = array();

	/**
	 * Compiler enabled indicator
	 * @var bool $enable
	 */	
	private $enable = true;

	/**
	 * View basepath
	 * Where the original view will put in
	 * @var bool $basepath
	 */	
	private $basepath;

	/**
	 * The list of resolved variables
	 * @var array|string $resolvedVariables
	 */
	private $resolvedVariables;

	/**
	 * The list of shared variables
	 * @var array|string $sharedVariables
	 */
	static $sharedVariables;

	/**
	 * The default view extension when compiler is disabled
	 * @var bool $extension
	 */	
	public $extension = Compiler::EXTENSION;

	/**
	 * Stream instance
	 * @var \Psr\Http\Message\StreamInterface $fs 
	 */
	private $fs;

	/**
	 * App instance
	 * @var \Repository\Component\Contracts\Container\ContainerInterface $app
	 */	
	private $app;
	
	/**
	 * Compiler instance
	 * @var \Repository\Component\View\Compiler\CompilerFactory $compiler
	 */
	private $compiler;

	/**
	 * @param \Psr\Http\Message\StreamInterface $fs 
	 * @param \Repository\Component\View\Compiler\CompilerFactory $compiler
	 */
	public function __construct(StreamInterface $fs, CompilerFactory $compiler)
	{
		$this->fs = $fs;
		$this->compiler = $compiler;
	}

	/**
	 * Get template compiler
	 * 
	 * @return \Repository\Component\View\Compiler\CompilerFactory
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

	/**
	 * Set template path
	 * 
	 * @param string|array $targets
	 * 
	 * @return \Repository\Component\View\View
	 */
	public function make(...$targets)
	{
		$this->targets = $targets;
		return $this;
	}

	/**
	 * Get template target path
	 * 
	 * @return string The template path
	 */	
	public function getTarget()
	{
		if (is_array($this->targets)) {
			$target = trim(implode(DS, $this->targets), DS);
		} else {
			$target = mb_strtolower(trim($this->targets), DS);
		}
		
		$target = $this->getTargetBasepath().$target;
		$extension = trim($this->extension, '.');
		$target = $target. DOT .$extension;

		return $target;
	}

	/**
	 * Set view to the specific target without any render messages
	 * 
	 * @param string|array $targets
	 * 
	 * @return void
	 */
	public function to($targets)
	{
		$this->make($targets);

		$this->handle();

		echo ltrim(ob_get_clean());
	}

	/**
	 * Set content variables to the specific view target and render it
	 * 
	 * @param string|array $variables List of variable name
	 * @param mixed $contents List of variable value
	 * 
	 * @return mixed
	 */	
	public function with($variables, $contents = array())
	{
		ob_start();

		$this->resolveVariable($variables, $contents);
		$this->handle($this->getResolvedVariables());

		echo ltrim(ob_get_clean());
	}

	/**
	 * Get content from specific view target without renderring it directly
	 * 
	 * @param string|array $variables List of variable name
	 * @param mixed $contents List of variable value
	 * 
	 * @return mixed
	 */	
	public function fetch($variables, $contents = array())
	{
		ob_start();

		$this->resolveVariable($variables, $contents);
		$this->handle($this->getResolvedVariables(), false);

		return ltrim(ob_get_clean());
	}

	/**
	 * Resolve defined variables before view is sent
	 * 
	 * @param string|array $variables
	 * @param string|array $contents
	 * 
	 * @return void
	 */
	public function resolveVariable($variables, $contents = array(), $shared = false)
	{
		if (is_array($variables)) {
			$collections = Collection::make(array());

			foreach ($variables as $variable) {

				if (Collection::make($contents)->offsetExists($variable)) {
					$collections->add($variable, $contents[$variable]);
				} else {
					for ($i=0; $i<count($variables); $i++) {
						$collections->add($variables[$i], $contents[$i]);
					}
				}
			}
			
			$this->addVariable($collections, $shared);
		} else {
			$collections = Collection::make(array());
			$collections->add($variables, $contents);
			$this->addVariable($collections, $shared);			
		}
	}
	
	/**
	 * Add variables to the view handler
	 * 
	 * @param \Repository\Component\Collection\Collection $variable The list of variable name
	 * @param bool $shared Treu determine if the variable shared, false otherwise
	 *  
	 * @return void
	 */
	private function addVariable(Collection $variable, $shared = false)
	{
		if (!$shared) {
			$this->resolvedVariables = $variable;
		} else {
			static::$sharedVariables[] = $variable;			
		}
	}

	/**
	 * Add shared variables to the view
	 * 
	 * @param string|array $variables The list of variable names
	 * @param string|array $contents Treu determine if the variable shared, false otherwise
	 *  
	 * @return void
	 */
	public function withShared($variables, $contents = array())
	{
		$this->resolveVariable($variables, $contents, true);
	}

	/**
	 * Register shared variables to the view handler
	 * 
	 * @param array $shareds The list of shared class
	 *  
	 * @return void
	 */	
	public function registerShared(array $shareds = array())
	{
		foreach ($shareds as $shared) {
			$shared = $this->createSharedInstance($shared);
			if ($shared instanceof ViewShared) {
				$shared->registerSharedVariable();
			}
		}
	}

	/**
	 * Register the application instance
	 * 
	 * @param \Repository\Component\Contracts\Container\ContainerInterface $app
	 *  
	 * @return void
	 */
	public function registerApp(ContainerInterface $app)
	{
		$this->app = $app;
	}

	/**
	 * Create shared instance by the given class name
	 * 
	 * @param string|\Repository\Component\View\ViewShared $className
	 *  
	 * @return \Repository\Component\View\ViewShared
	 */
	private function createSharedInstance($className)
	{
		if (is_string($className)) {
			$shared = new $className($this);
			if ($this->app !== null) {
				$shared->registerApp($this->app);
			}
		}
		
		return $shared;
	}

	/**
	 * Get defined shared variable
	 * 
	 * @return null|\Repository\Component\Collectio\Collection
	 */	
	public function getSharedVariable()
	{
		return static::$sharedVariables;
	}

	/**
	 * Get resolved variables
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public function getResolvedVariables()
	{
		$variables = $this->resolvedVariables;
		
		return $variables;
	}

	/**
	 * Handle renderring to the specific view target
	 * 
	 * @param \Repository\Component\Collection\Collection $collection The array collection instance
	 * @param bool $forceRequireOnce The determination where's the require must be required once or not
	 * 
	 * @throw \Exception
	 * Handle exception when something happen at the run time
	 * 
	 * @return void|null
	 */	
	private function handle(Collection $items = null, $forceRequireOnce = true)
	{
		$sharedCollection = $this->getSharedVariable();
		//Here we will extract shared variable to the entire view target
		//So with this shared variable we should able to use that defined variable
		//in the whole variety of view target as long as in the same class context
		if (!empty($sharedCollection)) {
			foreach ($sharedCollection as $collection) {
				if ($collection instanceof Collection) {
					$shareds = $collection->all();
					foreach ($shareds as $variable => $content) {
						${$variable} = $content;
					}
				}
			}
		}
		
		//With this variable we can't do the same things as shared variables
		//The following extracted variables only will available in the current view target
		//and not spreads into the whole view in the same class context
		//If you want share the unshared variable
		//you need use view helpers and pass the unshared variable to that argument parameter function helpers 
		if (null !== $items) {
			foreach ($items->all() as $variable => $content) {
				${$variable} = $content;
			}
		}
		
		//Here wee will define view helper
		//This `$view` callback helper can be use inside the view layer
		//without defining basepath and variable over and over again
		if (isset($view)) {
			$ex = "Can't use variable [\$view]. The variable have been reversed by compiler.";
			throw new ViewException($ex);
		}

		$view = $this->createViewCallback();

		$target = $this->getTarget();

		if (!$this->fs->exists($target)) {
			throw new ViewException("File [$target] not found.");
		}

		// We'll evaluate the contents of the view inside a try/catch block so we can
		// flush out any stray output that might get out before an error occurs or
		// an exception is thrown. This prevents any partial views from leaking.
		if ($this->isCompilerEnable()) {
			$this->compiler->make($target, $this
				->getTargetBasepath(), $this
				->getCacheBasepath()
			);
			
			try {
				$target = $this->compiler->getCompiledPath();

				if ($forceRequireOnce) {
					require_once $target;
				} else {
					require $target;
				}
			} catch (ViewException $ex) {
				$this->handleViewException($ex);
			}
		} else {
			try {
				if ($forceRequireOnce) {
					require_once $target;
				} else {
					require $target;
				}
			} catch (ViewException $ex) {
				$this->handleViewException($ex);
			}
		}
	}

	/**
	 * Do require template by the given template file
	 * 
	 * @param string $pathfile The template file
	 * @param bool $forceRequireOnce The determination where's the require must be required once or not
	 * 
	 * @return null
	 */
	private function doRequire($pathfile, $forceRequireOnce = true)
	{
		if ($forceRequireOnce) {
			require_once $pathfile;
		} else {
			require $pathfile;
		}
	}
	
	/**
	 * Create helper view callback
	 * 
	 * @return \Closure
	 */
	private function createViewCallback()
	{
		return function ($targets, $vars = array(), $contents = array()) {
			$view = ViewFactory::create();
			$view->setTargetBasepath($this->getTargetBasepath());

			if (!empty($vars)) {
				$view->make($targets)->with($vars, $contents);
				return;
			}

			$view->to($targets);
		};
	}

	/**
	 * Determine if the compiler is enabled
	 * 
	 * @return bool
	 */	
	public function isCompilerEnable()
	{
		if ($this->enable) return true;
		
		return false;
	}

	/**
	 * Set view extension when compiler is disabled
	 * 
	 * @param string $extension Ex. `.php` The extension should initialized with `.` dot
	 * 
	 * @return \Repository\Component\View\View
	 */		
	public function setExtension($extension = '')
	{
		$this->extension = empty($extension) ? $this->extension : $extension;
		
		return $this;
	}

	/**
	 * Handle exception on the output bufering
	 * 
	 * @throw \Repsotory\Component\View\Exception\ViewException
	 * Throw an exception when something happen at the run time
	 * 
	 * @return void
	 */	
	public function handleViewException(ViewException $ex)
	{
		ob_get_clean(); throw $ex;
	}

	/**
	 * Get view configuration based on the given group name
	 * 
	 * @param string $group Configuration group name
	 * 
	 * @return mixed
	 */	
	private function getViewParameter($group)
	{
		return Config::get('view')[$group];
	}

	/**
	 * Set view target basepath
	 * 
	 * @param string $basepath
	 * 
	 * @return \Repository\Component\View\View
	 */
	public function setTargetBasepath($basepath)
	{
		$this->basepath = $basepath;
	}

	/**
	 * Set custom view basepath
	 * 
	 * @return void
	 */
	public function basepath()
	{
		//Let the user defined class define their own basepath
	}

	/**
	 * Get default view target basepath
	 * 
	 * @return string
	 */	
	public function getTargetBasepath()
	{
		$basepath = $this->getViewParameter('basepath');

		if ($this->basepath() !== null) {
			$basepath = $this->basepath();
		}

		if ($this->basepath !== null) $basepath = $this->basepath;
		
		$this->basepath = $basepath;
		
		$basepath = SYSTEM_DIR_ROOT . trim($basepath, DS) . DS;
		
		return $basepath;
	}

	/**
	 * Get cache basepath
	 * 
	 * @return string
	 */		
	public function getCacheBasepath()
	{
		$basepath = trim($this->getViewParameter('cache'), DS);
		
		return $basepath;
	}

	public function disableCompiler()
	{
		$this->enable = false;
	}

	public function enableCompiler()
	{
		$this->enable = true;
	}
}
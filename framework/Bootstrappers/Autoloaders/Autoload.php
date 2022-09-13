<?php
namespace Repository\Component\Bootstrappers\Autoloaders;

/**
 * Class Autoloader.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Autoload
{	
	/**
	 * Class aliases container
	 * @var array $aliases
	 */
	private $aliases = array();

	/**
	 * Mapping traditional class container
	 * @var array $classmaps
	 */
	private $classmaps = array();

	/**
	 * Fully high qualified class name container
	 * @var array $namespaces
	 */
	private $namespaces = array();
	
	/**
	 * Initialize autoload and rootpath
	 * @param string $rootPath
	 */
	public function __construct(string $rootPath)
	{
		static::registerPhpExtensions();
		spl_autoload_register([$this, 'load']);
		$this->doRequire($rootPath . '/vendor/autoload');
	}

	/**
	 * Register php extensions.
	 * 
	 * @return void
	 */	
	public static function registerPhpExtensions(): void
	{
		spl_autoload_extensions('.php');
	}

	/**
	 * Get defined php autoload extensions.
	 * 
	 * @return array
	 */
	public static function getAutoloadExtensions(): array
	{
		return explode(',', spl_autoload_extensions());
	}

	/**
	 * Remove mapped class from container by the given class target.
	 * 
	 * @param string $classTarget
	 * 
	 * @return void
	 */
	public function remove($classTarget): void
	{
		if (isset($this->aliases[$classTarget])) {
			unset($this->aliases[$classTarget]);
		}
		
		if (isset($this->classmaps[$classTarget])) {
			unset($this->classmaps[$classTarget]);
		}

		if (isset($this->namespaces[$classTarget])) {
			unset($this->namespaces[$classTarget]);
		}
	}

	/**
	 * Add fully high qualified class name.
	 * 
	 * @param string $namespace
	 * @param string $namespacePath The path where the class live in
	 * 
	 * @return void
	 */	
	public function addNamespace(string $namespace, string $namespacePath): void
	{
		$this->namespaces[$namespace] = $namespacePath;
	}

	/**
	 * Add class alias
	 * 
	 * @param string $alias The class alias name
	 * @param string $concrete The concrete/original class name
	 * 
	 * @return void
	 */	
	public function addClassAlias(string $alias, string $concrete): void
	{
		$this->aliases[$alias] = $concrete;
	}

	/**
	 * Add traditional/unqualified class name.
	 * 
	 * @param string $className
	 * @param string $classPath The path where the class live in
	 * 
	 * @return void
	 */	
	public function addClass(string $className, string $classPath): void
	{
		$this->classmaps[$className] = $classPath;
	}

	/**
	 * Load class by the given class name target.
	 * The given target class name can either fully qualified, unqualified or alias class name.
	 * 
	 * @param string $classTarget
	 * 
	 * @return null
	 */
	public function load($calledClass)
	{
		//In the need for backward psr-0 compatibility
		//Here we'll replace the underscore character from the current called class
		//So we can use psr-0 namespace format as well
		$calledClass = str_replace('_', BS, $calledClass);
		$classPath = str_replace(BS, DS, $calledClass);
		$classPath = trim($classPath, DS);
		
		//When the called class is an alias. We just called the alias class from its original
		if (isset($this->aliases[$calledClass])) {
			return class_alias($this->aliases[$calledClass], $calledClass);
		}
		
		//Load called class from defined class map
		if (isset($this->classmaps[$calledClass])) {
			return $this->doRequire($this->classmaps[$calledClass]);
		}
		
		//Load called class from defined fully qualified class name
		foreach ($this->namespaces as $namespace => $path) {
			$namespace = trim($namespace, BS);
			$namespace = str_replace(BS, DOUBLE_BS, $namespace);
			$pattern = "/^".$namespace."/";
			
			if (preg_match($pattern, $calledClass)) {
				$path = trim($path, BS);
				$classPath = preg_replace($pattern, $path, $calledClass);
				$classPath = str_replace(BS, DS, $classPath);

				return $this->doRequire($classPath);
			}
		}
	}
	
	/**
	 * Do actual require by the given class path.
	 * 
	 * @param string $classPath
	 * 
	 * @return void
	 */
	public function doRequire(string $classPath): void
	{
		$classPaths = explode(DS, $classPath);
		$classPaths = array_slice($classPaths, 0);

		foreach ($this->getAutoloadExtensions() as $extension) {
			if (mb_strpos(end($classPaths), $extension) !== false) {
				if (file_exists($classPath)) {
					require $classPath;
				}
				
			} else {
				$classPath = $classPath . $extension;
				if (file_exists($classPath)) {
					require $classPath;
				}
			}
		}
	}
}
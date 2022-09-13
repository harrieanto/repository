<?php
namespace Repository\Component\Bootstrappers\Autoloaders;

use Repository\Component\Config\Config;

/**
 * PSR-4 Autoload Helper.
 *
 * @package	  \Repository\Component\Bootstrap
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Composer
{
	/**
	 * Psr-4 autoload list
	 * @var array $psr4Autoloads
	 */
	private $psr4Autoloads = array();

	/**
	 * The path root of our project directory
	 * @var string $rootPath
	 */
	private $rootPath;

	/**
	 * The path root where our psr-4 class live in
	 * @var string $psr4Path
	 */
	private $psr4Path;

	/**
	 * Initialize class configurations
	 * 
	 * @param array $psr4Autoloads
	 * @param string $rootPath
	 * @param string $psr4Path
	 */
	public function __construct(array $psr4Autoloads, string $rootPath, string $psr4Path)
	{
		$this->psr4Autoloads = $psr4Autoloads;
		$this->rootPath = $rootPath;
		$this->psr4Path = $psr4Path;
	}

	/**
	 * Create autoload mapper from composer.json
	 * 
	 * @param string $rootPath
	 * @param string $psr4Path
	 * 
	 * @return \Repository\Component\Bootstrap\Autoloaders\Composer
	 */	
	public static function createFromComposerJson($rootPath, $psr4Path)
	{

		$rootPath = rtrim($rootPath, Autoload::DS);
		$psr4Path = rtrim($psr4Path, Autoload::DS);

		$composerFile = trim($rootPath, Autoload::DS) . '/composer.json';
		$composerFile = Autoload::DS . $composerFile;

		if (file_exists($composerFile)) {
			$psr4Autoloads = json_decode(file_get_contents($composerFile), true);

			Config::set('composer', $psr4Autoloads);

			return new Composer($psr4Autoloads, $rootPath, $psr4Path);
		}
		
		return new Composer([], $rootPath, $psr4Path);
	}

	/**
	 * Get current namespace of the defined psr-4 path root
	 * 
	 * @return string|null
	 */
	public function getNamespace()
	{
		$psr4Autoloads = (array) Config::get('composer.autoload.psr-4');
		
		foreach ($psr4Autoloads as $namespace => $namespacePath) {
			$path = realpath($this->rootPath) . Autoload::DS . $namespacePath;
			$psr4Path = realpath($this->psr4Path);
			
			if ((realpath($this->rootPath) && realpath($this->psr4Path)) === false) {
				return null;
			}
			
			if (mb_strpos($path, $psr4Path) === 0) {
				return $namespace;
			}
		}
		
		return null;
	}

	/**
	 * Get the namespace path of current defined psr-4 path root
	 * 
	 * @return string|null
	 */
	public function getNamespacePath()
	{
		$namespace = $this->getNamespace();

		if ($namespace === null) {
			return null;
		}
		
		return $this->get('autoload.psr-4.'. $namespace);
	}

	/**
	 * Get composer raw configuration by the given segment
	 * 
	 * @param string $segment
	 * 
	 * @return mixed
	 */
	public static function get(string $segment)
	{
		return Config::get('composer.'. $segment);
	}

	/**
	 * Get fully qualified class name
	 * 
	 * @param string $className
	 * @param string $defaultNamespace
	 * 
	 * @return string
	 */
	public function getFullyQualifiedClassName(string $className, string $defaultNamespace = null)
	{
		$namespace = $this->getNamespace();
		
		if (mb_strpos($namespace, $className) === 0) {
			return $className;
		}
		
		return trim($defaultNamespace, Autoload::LS) . Autoload::LS . $className;
	}

	/**
	 * Get fully class path by the given namespace
	 * 
	 * @param string $fullyQualifiedNamespace
	 * 
	 * @return string
	 */
	public function getFullyClassPath(string $fullyQualifiedClassName)
	{
		$extensions = Autoload::getAutoloadExtensions();
		$path = trim($fullyQualifiedClassName, Autoload::LS);
		$path = str_replace(Autoload::LS, Autoload::DS, $fullyQualifiedClassName);
		$path = realpath($this->psr4Path) . Autoload::DS . $path . end($extensions);

		if (!file_exists($path)) {
			$parts = explode(Autoload::DS, $path);
			$paths[] = end($parts);
			array_unshift($paths, $this->psr4Path);
			
			return realpath(implode(Autoload::DS, $paths));
		}
		
		return $path;
	}
}
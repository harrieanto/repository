<?php
namespace Repository\Component\Filesystem;

use Repository\Component\Filesystem\Exception\FileNotFoundException;
use Repository\Component\Contracts\Filesystem\FilesystemInterface;
use Repository\Component\Http\FileStream;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use RuntimeException;
use Exception;

/**
 * Filesystem.
 *
 * @package	  \Repository\Component\Filesystem
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Filesystem extends FileStream implements FilesystemInterface
{
	/**
	 * Base directory of the given file
	 * @var string
	 */
	const DIR = __DIR__.self::DS;

	/**
	 * The double point separator
	 */
	const DIRECTORY_POINTING = '..';

	/**
	 * The single pointing
	 */
	const POINT = '.';
	
	/**
	 * The shorthand for directory separator
	 */
	const DS = DIRECTORY_SEPARATOR;

	/**
	 * The default file extension
	 * @var string
	 */
	public $extension 	= '.php';

	/**
	 * {@inheritdoc}
	 */
	public function make($input = Request::DEFAULT_BODY_STREAM, $mode = Request::MODE_READ)
	{
		parent::__construct($input, $mode);
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function extension(string $file, string $extension = '.php' )
	{
		$extPart = strstr($file, self::POINT);
		
		if ($extPart !== $extension) {
			$file = str_replace(self::POINT, self::DS, $file);
		}
		
		if (!empty($extension)) {
			if (!strpos($file, $extension)) {
				$file = $file.$extension;
			}
		}

		return $file;
	}

	/**
	 * {@inheritdoc}
	 */
	public function file($files = null, int $separator = 0, string $extension='.php')
	{
		if (is_array($files)) {
			return $this->files($files, $separator, $extension);
		}

		$extension = trim($this->extension($files, $extension), '/');
		
		return self::DIR.$this->separator($separator).$extension;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function files(array $files = array(), $separator = 0, $extension='.php')
	{
		foreach ($files as $directory => $file) {
			$extension	= $this->extension($file, $extension);

			$file = self::DIR.$this->separator($separator).$directory.self::DS.$extension;

			return $file;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function separator(int $separator = 0)
	{		
		switch ($separator) {
			case '0': return self::DS; break;
			case '1':
				return  self::DIRECTORY_POINTING.self::DS;
			break;
			default: 
				if ($separator > 1 ) {
					$separate = '';
					for ($no = 1; $no <= $separator; $no++) {
						 $separate .= self::DIRECTORY_POINTING.self::DS;
					}

					return $separate;
				};
			break;
		}
	}

	/**
	 * Determine if the accessed filee exist
	 * 
	 * @param string $name
	 * 
	 * @return bool
	 */
	public function exists($name)
	{
		return file_exists($name);
	}

	/**
	 * Get pathinfo
	 * 
	 * @param  string $pathName
	 * 
	 * @return array
	 */
	public function paths($pathName)
	{
		return pathinfo($pathName);
	}

	/**
	 * Delete the file at a given path.
	 *
	 * @param  string|array  $paths
	 * 
	 * @return bool
	 */
	public function delete($paths)
	{
		$paths = is_array($paths) ? $paths : func_get_args();

		$success = true;

		foreach ($paths as $path) {
			if (! @unlink($path)) $success = false;
		}

		return $success;
	}

	/**
	 * Move a file to a new location.
	 *
	 * @param  string  $path
	 * @param  string  $target
	 * 
	 * @return bool
	 */
	public function move($path, $target)
	{
		return rename($path, $target);
	}

	/**
	 * Extract the file name from a file path.
	 *
	 * @param  string  $path
	 * 
	 * @return string
	 */
	public function name($path)
	{
		return pathinfo($path, PATHINFO_FILENAME);
	}

	/**
	 * Extract the file extension from a file path.
	 *
	 * @param  string  $path
	 * 
	 * @return string
	 */
	public function getExtension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Get the file type of a given file.
	 *
	 * @param  string  $path
	 * 
	 * @return string
	 */
	public function type($path)
	{
		return filetype($path);
	}

	/**
	 * Get the file size of a given file.
	 *
	 * @param  string  $path
	 * 
	 * @return int
	 */
	public function size($path)
	{
		return filesize($path);
	}

	/**
	 * Get the file's last modification time.
	 *
	 * @param  string  $path
	 * 
	 * @return int
	 */
	public function lastModified($path)
	{
		return filemtime($path);
	}

	/**
	 * Get require content file
	 * 
	 * @param  string $filepath
	 * 
	 * @return mixed
	 */
	public function getRequire($filepath)
	{
		return require($filepath);
	}

	/**
	 * Get require once content file
	 * 
	 * @param  string $filepath
	 * 
	 * @return mixed
	 */
	public function getRequireOnce($filepath)
	{
		return require_once($filepath);
	}

	/**
	 * Get include content file
	 * 
	 * @param  string $filepath
	 * 
	 * @return mixed
	 */
	public function getInclude($filepath)
	{
		return include($filepath);
	}

	/**
	 * Get include once content file
	 * 
	 * @param  string $filepath
	 * 
	 * @return mixed
	 */
	public function getIncludeOnce($filepath)
	{
		return include_once($filepath);
	}

	/**
	 * Get dynamic constant by given class
	 * 
	 * @param  string $const identity of constants
	 * @param  string $class class name 
	 * 
	 * @return string
	 */
	public function getConst($const, $class = null)
	{
		return constant(sprintf('%s::%s', ($class !== null) ? $class : __CLASS__,  $const));
	}

	/**
	 * Read file into an array of lines with auto-detected line endings
	 * 
	 * @param  type $filepath location of file
	 * 
	 * @return array
	 */
	public function fileToArray($pathName)
	{
		ini_get('auto_detect_line_endings');

		ini_set('auto_detect_line_endings', '1');

		$collections = file($pathName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
 
		ini_set('auto_detect_line_endings', '1');

		return $collections;
	}

	/**
	 * Save an array to the file
	 *
	 * @param   string  $pathName   The target path
	 * @param   array   $array  The array to save
	 */
	public function saveArrayToFile($pathName, $array)
	{
		$content = "<?php \n"."return ".var_export($array, true).';';

		$this->putContent($pathName, $content);
	}

	/**
	 * Put content of the given file name
	 * 
	 * @param  string $pathName
	 * @param  string $content
	 * @param  bool  $lock
	 * 
	 * @return string
	 */
	public function putContent($pathName, $content, $lock = false)
	{
		if(!$this->exists($pathName)) {
			$this->make($pathName, FileStream::MODE_WRITE_READ);
		}

		file_put_contents($pathName, $content, $lock ? LOCK_EX : 0);
	}

	/**
	 * Append to a file.
	 *
	 * @param  string  $pathName
	 * @param  string  $content
	 * 
	 * @return int
	 */
	public function append($pathName, $content)
	{
		return file_put_contents($pathName, $content, FILE_APPEND);
	}

	/**
	 * Prepend to a file.
	 *
	 * @param  string  $pathName
	 * @param  string  $content
	 * 
	 * @return int
	 */
	public function prepend($pathName, $content)
	{
		if ($this->exists($pathName)) {
			return $this->putContent($path, $content.$this->getContent($pathName));
		}

		return $this->putContent($path, $data);
	}

	/**
	 * Get content of the given file
	 * 
	 * @param  string $path
	 * 
	 * @return string
	 */
	public function getContent($path)
	{
		if ($this->isFile($path)) {
			return file_get_contents($path);
		}
		
		throw new FileNotFoundException("File does not exist at path [$path]");
	}

	public function readFile(string $path)
	{
		$buffer = '';
		$iterator = $this->readFileGenerator($path);

		foreach ($iterator as $iteration) {
			$buffer .= $iteration;
		}
		
		return $buffer;
	}
	
	public function readFileGenerator(string $path)
	{
		if (file_exists($path)) {
			$handle = fopen($path, 'r');
			
			while (!feof($handle)) {
				yield fgets($handle);
			}
			
			fclose($handle);
		}
	}

	public function pipeFile(string $path, string $target)
	{
		$path1 = fopen($path, 'r');
		$path2 = fopen($target, 'r');

		stream_copy_to_stream($path1, $path2);

		fclose($path1);
		fclose($path2);
	}

	/**
	 * Copy source or file to specific path/directory
	 * 
	 * @param  path $source      main file/source
	 * @param  path $destination path file destination
	 * @param  path $directory   directory file, used to create one if not exists
	 * 
	 * @return boolean
	 */
	public function copy($source, $target, $directory = null)
	{
		//crate directory if not exists
		if (($directory !== null) && !is_dir($directory)) {
			mkdir($directory, 0777, true);
		}
		//get resource
		$source = file_get_contents($source);
		//open file destination
		$target = fopen($target, FileStream::MODE_WRITE_READ);
		//write resource into file destination
		fwrite($target, $source);
		//close resource
		fclose($target);
	}

	/**
	 * Internal function to find all directories at the path
	 *
	 * @param   string  $path  The path to look into
	 *
	 * @return  array   The paths with as they the last part of the path
	 */
	public function findDirs( $path )
	{
		$result = array();
		$fp 	= opendir($path);

		while (false !== ($file = readdir($fp))) {
			// Remove '.', '..'
			if (in_array($file, array('.', '..'))) {
				continue;
			}

			if (is_dir($path.'/'.$file)) {
				$result[$file] = $path.'/'.$file;
			}
		}

		closedir($fp);

		return $result;
	}

	/**
	 * Delete a file/recursively delete a directory
	 *
	 * NOTE: Be very careful with the path you pass to this!
	 *
	 * From: http://davidhancock.co/2012/11/useful-php-functions-for-dealing-with-the-file-system/
	 *
	 * @param string $path The path to the file/directory to delete
	 * 
	 * @return void
	 */
	public function deletes($path)
	{
		if (is_dir($path)) {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
				RecursiveIteratorIterator::CHILD_FIRST
			);

			foreach ($iterator as $file) {
				if ($file->isDir()) {
					rmdir($file->getPathname());
				} else {
					unlink($file->getPathname());
				}
			}

			rmdir($path);
			return true;
		} else {
			unlink($path);
			return true;
		}
	}

	/**
	 * Copy a file or recursively copy a directories contents
	 *
	 * From: http://davidhancock.co/2012/11/useful-php-functions-for-dealing-with-the-file-system/
	 *
	 * @param string $src The path to the source file/directory
	 * @param string $dst The path to the destination directory
	 * 
	 * @return void
	 */
	public function copies($src, $destination)
	{
		if (is_dir($src)) {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
				RecursiveIteratorIterator::SELF_FIRST
			);

			foreach ($iterator as $file) {
				if ($file->isDir()) {
					mkdir($destination.self::DS.$iterator->getSubPathName());
				} else {
					copy($file, $destination.self::DS.$iterator->getSubPathName());
				}
			}
		} else {
			$this->copy($src, $destination);
		}
	}

	/**
	 * Determine if the given path is a file.
	 *
	 * @param  string  $file
	 * 
	 * @return bool
	 */
	public function isFile($file)
	{
		return is_file($file);
	}

	/**
	 * Find path names matching a given pattern.
	 *
	 * @param  string  $pattern
	 * @param  int     $flags
	 * 
	 * @return array
	 */
	public function glob($pattern, $flags = 0)
	{
		return glob($pattern, $flags);
	}

	/**
	 * Determine if the given path is directory
	 * 
	 * @param $path
	 * 
	 * @return bool
	 */
	public function isDirectory($path)
	{
		return is_dir($path);
	}

	/**
	 * Current directory
	 * 
	 * @return string
	 */
	public function getDirectory()
	{
		return self::DS;
	}

	/**
	 * create new directory
	 * 
	 * @param  string  $name       directory name
	 * @param  integer $permitions
	 * @param  boolean $context
	 * 
	 * @return boolean
	 */
	public function createDir($name, $permitions = 777, $context = true)
	{
		if (is_dir($name)) {
			throw new \DomainException('Directory is existed one.');
		}

		return mkdir($name, $permitions, $context);
	}

	/**
	 * Remove dicrectory
	 * 
	 * @param  string $directory
	 * 
	 * @return boolean
	 */
	public function removeDir($directory)
	{
		return rmdir($directory);
	}
}

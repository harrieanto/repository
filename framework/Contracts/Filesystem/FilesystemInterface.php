<?php
namespace Repository\Component\Contracts\Filesystem;

/**
 * Filesystem Interface.
 * 
 * @package	 \Repository\Component\Contracts\Filesystem
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface FilesystemInterface
{
	/**
	 * 
	 * Set extension and check extension of any file
	 * by default, file will having php extension
	 * 
	 * @param  array|string  $file
	 * @param  boolean $exception optional
	 * @param  string $extension The default file extension
	 *
	 * @return string
	 */
	public function extension(string $file, string $extension = '.php');

	/**
	 * 
	 * Explicit file to seekExtension any user definied file
	 * 
	 * @param  string  $files      filename
	 * @param  integer $separator separator: default is 0
	 * 
	 * @return file
	 * 
	 */
	public function file($files = null, int $separator = 0, string $extension='.php');

	/**
	 * Directory separator
	 * 
	 * @param  integer $separator The number of separator: default is 0
	 * 
	 * @return string directory separator such as ../../ and more
	 * 
	 */
	public function separator(int $separator = 0);

	/**
	 * Get require once content file
	 * 
	 * @param  string $filepath
	 * 
	 * @return mixed
	 * 
	 */
	public function getRequireOnce($filepath);
}
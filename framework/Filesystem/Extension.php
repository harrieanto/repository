<?php
namespace Repository\Component\Filesystem;

use Repository\Component\contracts\Container\ContainerInterface;

/**
 * Reading File Extensions Info.
 *
 * @package	  \Repository\Component\Filesystem
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Extension
{
	/** The image gif mime type **/
	const IMAGE_GIF = 'image/gif';

	/** The image png mime type **/
	const IMAGE_PNG = 'image/png';

	/** The image bmp mime type **/
	const IMAGE_BMP = 'image/bmp';

	/** The image jpg mime type **/
	const IMAGE_JPG = 'image/jpg';

	/** The image jpeg mime type **/
	const IMAGE_JPEG = 'image/jpeg';

	/**
	 * List of extensions/mime types
	 * @var array $extensions
	 */
	public static $extensions = array(

		'csv'	=> 'text/csv', 
		'tsv'   => 'text/tab-separated-values', 
		'txt'	=> 'text/plain', 
		'htm'	=> 'text/html', 
		'html' 	=> 'text/html', 
		'md'	=> 'text/markdown', 
		'php' 	=> 'application/php', 
		'css'	=> 'text/css', 
		'js'	=> 'text/javascript', 
		'json'	=> 'application/json', 
		'xml'	=> 'application/xml', 
		'swf'	=> 'application/x-shockwave-flash', 
		'flv'	=> 'video/x-flv', 
		'ini'	=> 'zz-application/zz-winassoc-ini', 

		// images
		'png'	=> 'image/png', 
		'jpg'	=> 'image/jpeg', 
		'jpeg'	=> 'image/jpeg', 
		'gif'	=> 'image/gif',
		'bmp'	=> 'image/bmp',
		'ico'	=> 'image/vnd.microsoft.icon',
		'tiff'	=> 'image/tiff',
		'tif'	=> 'image/tiff',
		'svg'	=> 'image/svg+xml',
		'svgz'	=> 'image/svg+xml',

		// archives
		'zip' 	=> 'application/x-zip-compressed', 
		'rar'	=> 'application/x-rar-compressed',
		'exe'	=> 'application/x-msdownload',
		'msi'	=> 'application/x-msdownload',
		'cab'	=> 'application/vnd.ms-cab-compressed',

		// audio/video
		'mp3'	=> 'audio/mpeg',
		'qt'	=> 'video/quicktime',
		'mov'	=> 'video/quicktime',

		// adobe
		'pdf'	=> 'application/pdf',
		'psd'	=> 'image/vnd.adobe.photoshop',
		'ai'	=> 'application/postscript',
		'eps'	=> 'application/postscript',
		'ps'	=> 'application/postscript',

		// ms office
		'doc'	=> 'application/msword',
		'rtf'	=> 'application/rtf',
		'xls'	=> 'application/vnd.ms-excel',
		'ppt'	=> 'application/vnd.ms-powerpoint',

		// open office
		'odt'	=> 'application/vnd.oasis.opendocument.text',
		'ods'	=> 'application/vnd.oasis.opendocument.spreadsheet'
	);

	/**
	 * File info
	 * @var array
	 */
	public $fileInfo = array();

	/**
	 * Application instance
	 * @var \Repository\Component\contracts\Container\ContainerInterface $app
	 */
	public $app;

	public function __construct(ContainerInterface $app)
	{
		$this->app = $app;
	}

	/**
	 * Determine mime type/extension
	 * 
	 * @param  string $path
	 * 
	 * @return mixed
	 */
	public function getFileMimeType($path = null)
	{
		$fileInfo = (count($this->fileInfo) < 1)?
			$this->app['fs']->paths($path):
			$this->fileInfo;

		if (isset($fileInfo['extension']) && isset(self::$extensions[$fileInfo['extension']])) {
			return self::$extensions[$fileInfo['extension']];
		}

		return null;
	}

	/**
	 * Define path stream for checking
	 * 
	 * @param   string $context
	 * 
	 * @return Extension
	 */
	public function target($pathName, $extension = '.php', $separator = 3 )
	{
		$this->fileInfo = $this->app['fs']
			->paths($this
			->app['fs']
			->file($pathName, $separator, $extension)
		);

		return $this;
	}

	/**
	 * Set mime types by extension key
	 * 
	 * @param string $extension
	 * @param string $mime
	 * 
	 * @return array
	 */
	public function setMimeType($extension,  $mime)
	{
		return self::$extensions[$extension] = $mime;
	}

	/**
	 * Get mime type by key
	 * 
	 * @return array
	 */
	public function getMimeType($key)
	{
		return self::$extensions[$key];
	}

	/**
	 * Get extensions list
	 * 
	 * @return array
	 */
	public function getExtensions()
	{
		return self::$extensions;
	}
}
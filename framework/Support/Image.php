<?php
namespace Repository\Component\Support;

use Repository\Component\Support\Encoder;
use Repository\Component\Validation\Filter;
use Repository\Component\Filesystem\Extension;
use Repository\Component\Collection\Collection;
use Repository\Component\Filesystem\Filesystem as Fs;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Image Manipulation.
 * 
 * @package	  \Repository\Component\Support
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Image
{
	/**
	 * The default image quality
	 * @var int
	 */
	const IMAGE_QUALITY = 50;
	
	private $font;
	
	private $bgColor;
	
	private $textColor;
	
	private $imagePathFile;
	
	private $baseImage;
	
	private $fontSize = 25;
	
	/**
	 * Container instance
	 * @var Repository\Component\Contracts\Container\ContainerInterface $app
	 */
	private $app;

	/**
	 * Encoder instance
	 * @var Repository\Component\Support\Encoder $encoder
	 */
	private $encoder;

	/**
	 * Where the manipulated file should pupulated
	 * @var string $fileTarget
	 */
	private $fileTarget;

	/**
	 * Where the manipulated file should persisted
	 * @var string $fileTarget
	 */
	private $directory;

	/**
	 * Whether or not the target file shuffeled
	 * @var bool $randomize
	 */
	private $randomize = false;

	/**
	 * The original image path
	 * @var string $image
	 */
	private $image;

	/**
	 * Make new image instance
	 * @param Repository\Component\Contracts\Container\ContainerInterface $app
	 * @param Repository\Component\Support\Encoder $encoder
	 */
	public function __construct(ContainerInterface $app, Encoder $encoder)
	{
		$this->app = $app;
		$this->encoder = $encoder;
	}

	public function setRandomize(bool $randomize = false)
	{
		$this->randomize = $randomize;
	}

	/**
	 * Resize original image quality to the targeted image quality
	 * 
	 * @param int $quality The image quality desired
	 * @param bool $deleteAfterResized
	 * 
	 * @return void
	 */	
	public function resizeQuality($quality = Image::IMAGE_QUALITY, $deleteAfterResized = false)
	{
		//Get original image
		$imageSource = $this->getImage();
		//Get the extension from original image
		$extension = $this->getExtension();
		//Define manipulated image file

		$fileTarget = $this->getFileName() . "_{$quality}." .$extension;

		if ($this->randomize) {
			$fileTarget = md5($this->getFileName()) . '.' .$extension;
		}
		
		$target = $this->getDirectory() . DS . $fileTarget;
		$this->setReimageFile($target);

		switch (mime_content_type($imageSource)) {
			case Extension::IMAGE_PNG:
				$image = imagecreatefrompng($imageSource);
				imagealphablending($image, false);
				imagesavealpha($image, true);
				imagepng($image, $target, 9);
			break;
			case Extension::IMAGE_JPEG:
				$image = imagecreatefromjpeg($imageSource);
				imagejpeg($image, $target, $quality);
			break;
			case Extension::IMAGE_GIF:
				$image = imagecreatefromgif($imageSource);
				imagealphablending($image, false);
				imagesavealpha($image, true);
				imagegif($image, $target, $quality);
			break;
		}

		//This will automatically delete original image soon after image have manipulated
		if ($deleteAfterResized) $this->remove();
		imagedestroy($image);

		return true;
	}
	
	public function createBaseImage(int $width = 50, int $height = 50)
	{
		$this->baseImage = imagecreatetruecolor($width, $height);
	}

	public function setBgColor(int $r = 255, int $g = 255, int $b = 255)
	{
		$this->bgColor = imagecolorallocate($this->getBaseImage(), $r, $g, $b);
		$this->fillColorToImage($this->bgColor);
	}
	
	public function setTextColor(int $r = 0, int $g = 0, int $b = 0)
	{
		$this->textColor = imagecolorallocate($this->getBaseImage(), $r, $g, $b);
	}
	
	public function fillColorToImage(int $color, int $x = 0, int $y = 0)
	{
		imagefill($this->getBaseImage(), $x, $y, $color);
	}
	
	public function setFont(string $fontPath)
	{
		$this->font = $fontPath;
	}
	
	public function getFont()
	{
		return $this->font;
	}

	public function setFontSize(int $size = 25)
	{
		$this->fontSize = $size;
	}
	
	public function getFontSize()
	{
		return $this->fontSize;
	}
	
	public function createPngAvatarFromString(string $text)
	{
		$imageFilePath = $this->getImagePathFile();
		
		if ($imageFilePath === null) {
			throw new \Exception("You must define image file path target!");
		}
		
		$text = wordwrap($text, 4, "\n");
		
		list($x, $y) = $this->getCenterCoords($text);
		
		imagettftext($this
			->getBaseImage(), $this
			->getFontSize(), 0, $x, $y, $this
			->getTextColor(), $this
			->getFont(), $text);
		
		imagepng($this->getBaseImage(), $imageFilePath);
		imagedestroy($this->getBaseImage());
		
		return $imageFilePath;
	}
	
	public function getCenterCoords(string $text)
	{
		$bbox = imagettfbbox($this->getFontSize(), 0, $this->getFont(), $text);
		
		$textWidth = abs(max($bbox[2], $bbox[4]));
		$textHeight = abs(max($bbox[5], $bbox[7]));
		
		$imageWidth = imagesx($this->getBaseImage());
		$imageHeight = imagesy($this->getBaseImage());
		
		$x = intval(($imageWidth - $textWidth) / 2);
		$y = intval(($imageHeight + $textHeight) / 2);		
		
		return array($x, $y);
	}
	
	public function getBaseImage()
	{
		if (!is_resource($this->baseImage)) {
			throw new \Exception("Base image must be resource");
		}
		
		return $this->baseImage;
	}
	
	public function getTextColor()
	{
		$color = imagecolorallocate($this->getBaseImage(), 0, 0, 0);
		
		return $this->textColor === null ? $color : $this->textColor;
	}

	public function getBgColor()
	{
		$color = imagecolorallocate($this->getBaseImage(), 255, 255, 255);
		
		return $this->bgColor === null ? $color : $this->bgColor;
	}

	/**
	 * Encode image by the given image source or by the given original image
	 * 
	 * @param string|null $source The path too the image desired
	 * 
	 * @return string Encoded image in base64
	 */	
	public function encodeImage($source = null)
	{
		if (Filter::isNull($source)) {
			$extension = $this->getExtension();
			$image = call_user_func(
				$this->encodeBase64(), 
				$this->image, 
				$extension);
			
			return $image;
		}
		
		$extension = $this->app['fs']->getExtension($source);

		$image = call_user_func(
			$this->encodeBase64(), 
			$source, 
			$extension
		);
		
		return $image;
	}

	/**
	 * Encode image formatted in base64 encoder
	 * 
	 * @return \Closure
	 */
	private function encodeBase64()
	{
		return function ($source, $extension) {
			$source = file_get_contents($source);
			$encodedImage = $this->encoder->encode($source, Encoder::BASE_64);
			$encodedImage = "data:image/$extension;base64,".$encodedImage;
		
			return $encodedImage;
		};
	}

	/**
	 * Set image by the given target path
	 * 
	 * @param string $pathFile The image file
	 * 
	 * @return void
	 */
	public function setImage($pathFile)
	{
		if (!$this->app['fs']->isFile($pathFile) && !$this->app['fs']->exists($pathFile)) {
			throw new \RuntimeException("The given image [$pathFile] is invalid");
		}

		$pathFile = str_replace('/', DS, $pathFile);
		$this->image = str_replace('\\\\', DS, $pathFile);
	}

	/**
	 * Get defined image path file
	 * 
	 * @return string
	 */	
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * Set directory where the manipulated image should be put in
	 * 
	 * @param string $dir The path to the specific directory name
	 * 
	 * @return void
	 */
	public function setDirectory($dir)
	{
		if ($this->app['fs']->isFile($dir))
			throw new \RuntimeException("The given directory is invalid");
		
		if (!$this->app['fs']->isDirectory($dir))
			throw new \RuntimeException("The given directory is not available");
		
		$this->directory = $dir;
	}
	
	public function getDirectory()
	{
		return $this->directory;
	}

	/**
	 * Set manipulated image resource to the specific file
	 * 
	 * @param string $fileName The image file
	 * 
	 * @return void
	 */	
	public function setReimageFile(string $fileName)
	{
		$target = trim($fileName, DS);
		$this->fileTarget = $fileName;
	}

	/**
	 * Get manipulated image resource
	 * 
	 * @return The manipulated image file
	 */	
	public function getReimageFile()
	{
		return $this->fileTarget;
	}

	public function setImagePathFile(string $fileName)
	{
		$this->imagePathFile = $fileName;
	}
	
	public function getImagePathFile()
	{
		return $this->imagePathFile;
	}

	/**
	 * Get origiinal image info
	 * 
	 * @return array When suckseed, false otherwise
	 */		
	public function getInfo()
	{
		$info = Collection::make(array());
		
		$images = exif_read_data($this->image);

		foreach ($images as $identifier => $value) {
			$info->add($identifier, $value);
		}
		
		return $info;
	}

	/**
	 * Get image eextension by the given image mime type
	 * 
	 * @param string|null $mimeType
	 * 
	 * @return string
	 */		
	public function getExtension($mimeType = null)
	{
		$extension = $this->app['fs']->getExtension($this->image);
		
		return $extension;
	}

	/**
	 * Remove original image
	 * 
	 * @return void
	 */	
	public function remove()
	{
		$this->app['fs']->delete($this->getImage());
	}

	/**
	 * Get image mime type
	 * 
	 * @return string
	 */		
	public function getMimeType()
	{
		$extensions = Extension::$extensions;
		
		if (array_key_exists($ext = $this->getExtension(), $extensions)) {
			return $extensions[$ext];
		}
	}

	/**
	 * Get original image file name
	 * 
	 * @return string|null
	 */		
	public function getFileName()
	{
		$name = $this->app['fs']->name($this->image);
		
		return $name;
	}
}
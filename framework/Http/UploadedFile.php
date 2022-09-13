<?php
namespace Repository\Component\Http;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;
use Repository\Component\Filesystem\Extension;

/**
 * PSR-7 Upload Handler.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class UploadedFile implements UploadedFileInterface
{
	/**
	 * Uploaded file info
	 * @var array $info
	 */
	protected $info;

	/**
	 * Uploaded File Name
	 * @var string|null $movedName
	 */
	protected $movedName = null;

	/**
	 * FileStream instance
	 * @var \Psr\Http\Message\StreamInterface $stream
	 */
	protected $stream;

	/**
	 * Indicate that uploaded file name will shuffled automatically
	 * @var bool $random
	 */
	protected $random = true;

	/**
	 * @param array $info
	 * @param bool $random
	 */
	public function __construct(array $info, $random = true)
	{
		$this->info = $info;
		$this->random = $random;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UploadedFileInterface::getStream()
	 */
	public function getStream()
	{
		if (!$this->stream) {
			if ($this->movedName) {
				$this->stream = new FileStream($this->movedName);
			} else {
				$this->stream = new FileStream($this->info['tmp_name']);
			}
		}

		return $this->stream;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UploadedFileInterface::moveTo()
	 */
	public function moveTo($targetPath)
	{
		$target = $this->getClientFilename();

		if ($this->isRandom()) {
			$target = md5($this->info['name']) . '.' . $this->getFileExtension();
		}
		
		$targetName = $target;
		$targetPath = trim($targetPath, DS);
		$target = SYSTEM_DIR_ROOT. $targetPath . DS . $target;
		$target = str_replace(array('//', '\\'), array('//', '\\'), $target);

		if ($this->getError() !== UPLOAD_ERR_OK) {
			throw new \RuntimeException('Impossible to move file: the uploaded file has an error.');
		}

		if (is_uploaded_file($this->getInfo('tmp_name'))) {
			if (!move_uploaded_file($this->getInfo('tmp_name'), $target)) {
				return false;
			}
		} else {
			if (!@rename($this->getInfo('tmp_name'), $target)) {
				return false;
			}
		}

		$this->movedName = $targetName;

		return true;
	}

	/**
	 * Determine if the uplooaded filename will shuffled
	 *  
	 * @return bool
	 */
	private function isRandom()
	{
		if ($this->random) return true;
		
		return false;
	}

	/**
	 * Get uploaded file info by the given key
	 *  
	 * @param string $key
	 * 
	 * @return string Uploaded file info
	 */
	private function getInfo($key)
	{
		$info = $this->info;
		
		return $info[$key];
	}

	/**
	 * Get uploaded file extension based on client media type
	 *  
	 * @return string File extension
	 */
	private function getFileExtension()
	{
		$clientMediaType = $this->getClientMediaType();
		$extensions = array_flip(Extension::$extensions);

		if (array_key_exists($clientMediaType, $extensions)) {
			return $extensions[$clientMediaType];
		}
		
		return pathinfo($this->getClientFilename(), PATHINFO_EXTENSION);
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UploadedFileInterface::getMovedName()
	 */
	public function getMovedName()
	{
		return $this->movedName ?? null;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UploadedFileInterface::getSize()
	 */
	public function getSize()
	{
		$size = $this->getInfo('size');
		
		return $size ?? null;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UploadedFileInterface::getError()
	 */
	public function getError()
	{
		if (!$this->movedName) {
			return UPLOAD_ERR_OK;
		}
		
		return $this->getInfo('error');
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UploadedFileInterface::getClientFilename()
	 */
	public function getClientFilename()
	{
		$clientName = $this->getInfo('name');

		return $clientName ?? null;
	}

	/**
	 * {@inheritdoc}
	 * See \Psr\Http\Message\UploadedFileInterface::getClientMediaType()
	 */
	public function getClientMediaType()
	{
		$mediaType = $this->getInfo('type');

		return $mediaType ?? null;
	}
}
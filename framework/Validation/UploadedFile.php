<?php
namespace Repository\Component\Validation;

use Exception;
use Psr\Http\Message\UploadedFileInterface;
use Repository\Component\Filesystem\Extension;
use Repository\Component\Collection\Collection;
use Repository\Component\Http\UploadedFile as PsrUploadedFile;

/**
 * Uploaded File Validation.
 * 
 * @package	  \Repository\Component\Validation
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class UploadedFile
{
	/**
	 * Uploaded target directory
	 * @var string $target
	 */	
	protected $target;

	/**
	 * Validation rule instance
	 * @var \Repository\Component\Validation\Rule $rule
	 */	
	protected $rule;

	/**
	 * Validation alert instance
	 * @var \Repository\Component\Validation\Alert $alert
	 */	
	protected $alert;

	/**
	 * Determine whether or not uploaded file name should be random
	 * @var bool $random
	 */	
	protected $random = true;

	/**
	 * Uploaded filename container
	 * @var bool|array $uploadedFilenames
	 */		
	protected $uploadedFilenames;

	/**
	 * Allowed uploaded file etxensions
	 * @var array $extensions
	 */		
	protected static $extensions = array(
		'gif' => Extension::IMAGE_GIF, 
		'png' => Extension::IMAGE_PNG, 
		'jpg' => Extension::IMAGE_JPG, 
		'jpeg' => Extension::IMAGE_JPEG, 
		'bmp' => Extension::IMAGE_BMP, 
	);

	/**
	 * Uploaded file instance
	 * @var \Psr\Http\Message\UploadedFile $uploadedFile
	 */	
	protected $uploadedFile;

	/**
	 * @param \Repository\Component\Validation\Rule $rule
	 * @param \Repository\Component\Validation\Alert $alert
	 */	
	public function __construct(Rule $rule, Alert $alert)
	{
		$this->rule = $rule;
		$this->alert = $alert;
	}

	/**
	 * Initialize uploaded file instance
	 * 
	 * @param array $uploadedFiles
	 *  
	 * @return void
	 */	
	public function initialize($uploadedFiles)
	{
		$this->uploadedFiles = $uploadedFiles;
	}
	
	/**
	 * Create concrete Uploaded File handler
	 * 
	 * @param array $attributes The $_FILES attributes
	 *  
	 * @return \Psr\Http\Message\UploadedFileInterface
	 * 
	 */
	private function createUploadedFileHandler($attributes)
	{
		return new PsrUploadedFile((array) $attributes, $this->isRandom());
	}

	/**
	 * Determine if the uplooaded filename should be shuffled
	 *  
	 * @return bool
	 */	
	public function isRandom()
	{
		if ($this->random) {
			return true;
		}
		
		return false;
	}

	/**
	 * Set randomize for uploaded filename
	 * 
	 * @param bool $random
	 *  
	 * @return void
	 * 
	 */	
	public function setRandomize(bool $random)
	{
		$this->random = $random;
	}

	/**
	 * Resolve uploaded file alert validation
	 *  
	 * @return void
	 */	
	public function resolveUploadedFile()
	{
		$uploadedFiles = $this->uploadedFiles;
		$sortedUploadedFile = $this->sortUploadedFiles($uploadedFiles);
		$uploadedFiles = Collection::make($sortedUploadedFile);

		$uploadedFiles->map(function($attributes) {
			$this->uploadedFile = $this->createUploadedFileHandler($attributes);
			$alertLevel = $attributes['error'];
			$this->resolvePartialAlert($alertLevel);
			$this->resolveFormSizeAlert($alertLevel);
			$this->resolveIniSizeAlert($alertLevel);
			
			if (preg_match('/(.*)\.zip/', $attributes['name'])) {
				$attributes['type'] = Extension::$extensions['zip'];
			}
			
			$this->resolveMediaTypeAlert($attributes['type']);

			if ($this->hasAllowedFileType($attributes['type'])) {
				$this->uploadedFile->moveTo($this->getTarget());
				$this->resolveFailedAlert($alertLevel);
			}
		});
	}

	/**
	 * Sort single uploaded file to the multiple hirarchy
	 * 
	 * @param array $uploadedFilesInfo
	 *  
	 * @return array Sorted uploaded files
	 */	
	protected function sortUploadedFile(array $uploadedFileInfo)
	{
		$hirarchies = array();
		$hirarchies[] = $uploadedFileInfo;
		
		return $hirarchies;
	}

	/**
	 * Sort multiple uploaded file to the multiple hirarchy
	 * 
	 * @param array $uploadedFilesInfo
	 *  
	 * @return array Sorted uploaded files
	 */
	protected function sortUploadedFiles(array $uploadedFileInfo)
	{
		$hirarchies = array();
		
		if (isset($uploadedFileInfo)) {
			$upload = &$uploadedFileInfo;

			if (!is_array($upload['error'])) {
				return $this->sortUploadedFile($upload);
			}

			foreach ($upload['error'] as $key => $info) {
				if($upload['error'][$key] === UPLOAD_ERR_OK) {
					$uploadedFileInfo['tmp_name'] = $upload['tmp_name'][$key];
					$uploadedFileInfo['name'] = $upload['name'][$key];
					$uploadedFileInfo['size'] = $upload['size'][$key];
					$uploadedFileInfo['type'] = $upload['type'][$key];
					$uploadedFileInfo['error'] = $upload['error'][$key];
					unset($uploadedFileInfo[Validation::$uploadedField]);
					
					$hirarchies[] = $uploadedFileInfo;
				}
			}
		}
		
		return $hirarchies;
	}

	/**
	 * Determine if the uploaded file contains allowed media type
	 * 
	 * @param string $type
	 *  
	 * @return void
	 */		
	public function hasAllowedFileType($type)
	{
		$types = Collection::make(self::$extensions);

		if ($types->contains($type)) return true;

		return false;
	}

	/**
	 * Add allowed media type to the list upload
	 * 
	 * @param string $extension The file extension
	 * @param string $mimeType The file mime type
	 *  
	 * @return void
	 */		
	public function addAllowedFileType($extension, $mimeType)
	{
		self::$extensions[$extension] = $mimeType;
		
		return $this;
	}
	
	public function flushAllowedFileType()
	{
		self::$extensions = array();
	}
	
	/**
	 * Set succesful uploaded filename to the container
	 * 
	 * @param string $filename
	 *  
	 * @return void
	 */
	public function setUploadedFilename($filename)
	{
		$this->uploadedFilenames[] = $filename;
	}

	/**
	 * Get uploaded file name
	 * 
	 * @return null|array Null indicate that no file uploaded/failed
	 */	
	public function getUploadedFilenames()
	{
		$fileNames = $this->uploadedFilenames;
		
		return $fileNames;
	}

	/**
	 * Resolve upload error partial alert by the given key
	 * 
	 * @param string $key
	 *  
	 * @return void
	 */		
	public function resolvePartialAlert($key)
	{
		$alert = $this->alert->getMessage(UPLOAD_ERR_PARTIAL);

		if ($key === UPLOAD_ERR_PARTIAL)
			$this->alert->setValidationAlert(
				Validation::$uploadedField, 
				$alert
			);
	}

	/**
	 * Resolve upload error form size alert by the given key
	 * 
	 * @param string $key
	 *  
	 * @return void
	 */		
	public function resolveFormSizeAlert($key)
	{
		$alert = $this->alert->getMessage(UPLOAD_ERR_FORM_SIZE);

		if ($key === UPLOAD_ERR_FORM_SIZE)
			$this->alert->setValidationAlert(
				Validation::$uploadedField, 
				$alert
			);
	}

	/**
	 * Resolve upload error ini size alert by the given key
	 * 
	 * @param string $key
	 *  
	 * @return void
	 */		
	public function resolveIniSizeAlert($key)
	{
		$alert = $this->alert->getMessage(UPLOAD_ERR_INI_SIZE);

		if ($key === UPLOAD_ERR_INI_SIZE)
			$this->alert->setValidationAlert(
				Validation::$uploadedField, 
				$alert
			);
	}

	/**
	 * Resolve not allowed media type alert
	 * 
	 * @param string $type Uploaded file media type
	 *  
	 * @return void
	 */			
	public function resolveMediaTypeAlert($type)
	{
		$alert = $this->alert->getMessage('not-allowed-media-type');
		
		if (!$this->hasAllowedFileType($type))
			$this->alert->setValidationAlert(
				Validation::$uploadedField, 
				$alert
			);
	}

	/**
	 * Resolve upload failed alert by the given key
	 * 
	 * @param string $key
	 *  
	 * @return void
	 */			
	public function resolveFailedAlert($key)
	{
		$uploadedFile = $this->uploadedFile->getMovedName();
		$alert = $this->alert->getMessage(UPLOAD_ERR_NO_FILE);
		
		//If moved name is empty
		//it's means something was wrong with uploaded file		
		//So we just set an alert to indicate that uploaded file was failed
		if (empty($uploadedFile)) {
			$this->alert->setValidationAlert(
				Validation::$uploadedField, 
				$alert
			);
			
			return;
		}
		
		//Set uploaded filename to the list
		$this->setUploadedFilename($uploadedFile);
	}

	/**
	 * Set uploaded file directory target
	 * 
	 * @param string $target
	 *  
	 * @return void
	 */			
	public function setTarget($target)
	{
		if ($target !== null && !is_dir($target)) {
			mkdir($target, 0755, true);
		}

		$this->target = $target;
	}

	/**
	 * Get uploaded file directory target
	 * 
	 * @return string
	 */				
	public function getTarget()
	{
		$target = $this->target;
		
		return $target;
	}
}
<?php
namespace Repository\Component\Validation;

use Repository\Component\Hashing\Hash;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Validation Factory.
 * 
 * @package	  \Repository\Component\Validation
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Validation
{
	/** The pipe character **/
	const PIPE = "|";

	/** The minimal indicator pattern **/	
	const MIN = "min:";

	/** The maximal indicator pattern **/	
	const MAX = "max:";

	/** The equal indicator pattern **/
	const EQUAL = "equal:";

	/**
	 * The uploaded field
	 * @var string $uploadedField
	 */
	public static $uploadedField = "attachment";

	/**
	 * The password field
	 * @var string $passwordField
	 */
	public static $passwordField = "password";

	/**
	 * @inheritdoc
	 * See \Repository\Component\Validation\Alert
	 */	
	public function __construct(
		ContainerInterface $app, 
		Rule $rule, 
		Hash $hash)
	{
		$validation = new Alert($app, $rule, $hash);
		
		$this->validation = $validation;
	}

	/**
	 * @inheritdoc
	 * See \Repository\Component\Validation\Alert::make
	 */	
	public function make(array $patterns)
	{
		$this->validation->make($patterns);
		
		return $this;
	}

	/**
	 * @inheritdoc
	 * See \Repository\Component\Validation\Alert::alerts
	 */	
	public function alerts()
	{
		$alerts = $this->validation->alerts();
		
		return $alerts;
	}
	
	/**
	 * @inheritdoc
	 * See \Repository\Component\Validation\Alert::alert
	 */
	public function alert($key)
	{
		$alert = $this->validation->alert($key);
		
		return $alert;
	}

	/**
	 * Determine if the whole request is validated true
	 * 
	 * @return bool True indicate that validation passed oke
	 * False otherwise
	 */	
	public function isValidated()
	{
		if (empty($this->validation->alerts()))
			return true;
		
		return false;
	}

	/**
	 * @inheritdoc
	 * See \Repository\Component\Validation\Alert::allRequest
	 */	
	public function allRequest()
	{
		$requests = $this->validation->allRequest();
		
		return $requests;
	}

	/**
	 * Set uploaded file directory target
	 * 
	 * @param string $target
	 *  
	 * @return void
	 */
	public function setUploadedTarget($target)
	{
		$uploadedFile = $this->getUploadedFileInstance();
		
		$uploadedFile->setTarget($target);
	}

	/**
	 * Get uploaded file directory target
	 * 
	 * @return string
	 */
	public function getUploadedTarget()
	{
		$uploadedFile = $this->getUploadedFileInstance();
		
		$target = $uploadedFile->getTarget();
		
		return $target;
	}

	/**
	 * @inheritdoc
	 * See \Repository\Component\Validation\UploadedFile::getUploadedFilenames
	 */
	public function getUploadedFilenames()
	{
		$uploadedFile = $this->getUploadedFileInstance();
		
		$fileNames = $uploadedFile->getUploadedFilenames();
		
		return $fileNames;
	}


	/**
	 * @inheritdoc
	 * See \Repository\Component\Validation\UploadedFile::addAllowedFileType
	 */
	public function addAllowedFileType($extension, $mimeType)
	{
		$uploadedFile = $this->getUploadedFileInstance();
		$uploadedFile->addAllowedFileType($extension, $mimeType);
	}

	/**
	 * Get UploadedFile instance
	 * @return \Repository\Component\Validation\UploadedFile
	 */	
	public function getUploadedFileInstance()
	{
		$uploadedFile = $this->validation->getUploadedFileInstance();
		
		return $uploadedFile;
	}
}
<?php
namespace Repository\Component\Http;

/**
 * Download Response.
 *
 * @package	  \Repository\Component\Http
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class DownloadResponse extends Response
{
	/**
	 * @param string $filePath The path to file want to be transfer
	 * @param string $fileName The custom file name
	 * @param string $disposition The content disposition
	 */
	public function __construct(string $filePath, string $fileName = null, $disposition = 'attachment')
	{
		$stream = new FileStream($filePath);
		
		if (!$fileName) {
			$fileName = $stream->getInfo()->getFilename();
		}
		
		parent::__construct(200, $stream, array(
			'Cache-Control' => 'must-revalidate', 
			'Content-Description' => 'File Transfer', 
			'Content-Disposition' => sprintf('%s; filename="%s"', $disposition, $fileName), 
			'Content-Length' => $stream->getSize(), 
			'Content-Type' => mime_content_type($filePath), 
			'Expires' => 0, 
			'Pragma' => 'public', 
		));
	}
}
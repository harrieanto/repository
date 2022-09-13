<?php
namespace Repository\Component\Auth\Jwt;

use InvalidArgumentException;
use Repository\Component\Support\Encoder;
use Repository\Component\Auth\Jwt\Signer\Algorithm;

/**
 * Jwt Header.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JwtHeader
{
	/**
	 * Jwt header specifications
	 * @var array $headers
	 */
	private $headers = array(
		'typ' => 'JWT', 
		'alg' => 'HS256', 
	);

	/**
	 * @param string $algorithm The header algorithm
	 * @param array $headers Additional header specfication
	 */
	public function __construct($algorithm = 'HS256', array $headers = array())
	{
		$algorithm = strtoupper($algorithm);
		$this->setAlgorithm($algorithm);
		
		foreach ($headers as $name => $value) {
			$this->add($name, $value);
		}
	}

	/**
	 * Add header to the header list
	 * 
	 * @param string $name The header name
	 * @param array|string $value The header value
	 * 
	 * @return void
	 */	
	public function add(string $name, $value)
	{
		switch ($name) {
			case 'alg':
				$this->setAlgorithm($value);
				break;
			default:
				$this->headers[$name] = $value;
				break;
		}
	}

	/**
	 * Change alorithm header
	 * 
	 * @param string $value
	 * 
	 * @return void
	 */	
	public function setAlgorithm(string $value)
	{
		if (!array_key_exists($value, Algorithm::getAll())) {
			throw new InvalidArgumentException("Algorithm [$value] not supported");
		}
		
		$this->headers['alg'] = $value;
	}

	/**
	 * Get defined algorithm header
	 * 
	 * @return string The algorithm value
	 */
	public function getAlgorithm()
	{
		return $this->headers['alg'];
	}

	/**
	 * Set content type header
	 * 
	 * @param string $value
	 * 
	 * @return void
	 */	
	public function setContentType(string $value)
	{
		$this->headers['cty'] = $value;
	}

	/**
	 * Get defined content type
	 * 
	 * @return string|null
	 */	
	public function getContentType()
	{
		if (isset($this->headers['cty'])) {
			return $this->headers['cty'];
		}
	}

	/**
	 * Get all defined headers
	 * 
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Encode all defined headers
	 * 
	 * @return string The encoded header
	 */
	public function encode()
	{
		$header = json_encode($this->getHeaders());
		$encodedHeader = Encoder::base64UrlEncode($header);
		
		return $encodedHeader;
	}
}
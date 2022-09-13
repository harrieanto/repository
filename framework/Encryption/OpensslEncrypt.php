<?php
namespace Repository\Component\Encryption;

use Repository\Component\Support\Str;
use Repository\Component\Support\Encoder;
use Repository\Component\Encryption\Exception\EncryptionException;
use Repository\Component\Contracts\Encryption\EncryptionInterface;

/**
 * Openssl Encryption.
 *
 * @package	  \Repository\Component\Encryption
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class OpensslEncrypt implements EncryptionInterface
{
	/** The encoder mode **/
	const ENCODE_MODE = Encoder::BASE_64;
	
	/**
	 * The encryption key
	 * @var string $key
	 */
	private $key;

	/**
	 * The encryption cipher
	 * @var string $cipher
	 */	
	private $cipher;

	/**
	 * The endcoder helper
	 * @var \Repository\Component\Support\Encoder $encoder
	 */	
	private $encoder;

	/**
	 * Create new ecnryption instance
	 * 
	 * @param string $key
	 * @param string $cipher
	 */	
	public function __construct($key, Encoder $encoder, $cipher = "AES-256-CBC")
	{
		$ex = "Unsupported [$cipher] cipher Encryption";

		$cipher = Str::lower($cipher);
		$cipher = new CipherMethods($cipher);

		if (!$cipher->isAvailable()) {
			throw new EncryptionException($ex);
		}

		$this->key = $key;
		$this->cipher = $cipher->getCipher();
		$this->encoder = $encoder;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Encryption\EncryptionInterface::encrypt
	 */	
	public function encrypt($value)
	{
		$payloads = array();

		$ivLength = openssl_cipher_iv_length($this->cipher);
		$iv = Str::randomBytes($ivLength);
		$value = openssl_encrypt($value, $this->cipher, $this->key, 0, $iv);
		
		$ex = "Failed encrypting the given value";

		if (!$value) {
			throw new EncryptionException($ex);
		}

		$value = $this->encoder->encode($value, self::ENCODE_MODE);

		$payloads['value'] = $value;
		$payloads['iv'] = $this->encoder->encode($iv, self::ENCODE_MODE);
		$payloads = json_encode($payloads);

		if (json_last_error() !== 0) {
			throw new EncryptionException($ex);
		}
		
		return $this->encoder->encode(
			$payloads, 
			self::ENCODE_MODE
		);
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Encryption\EncryptionInterface::decrypt
	 */	
	public function decrypt($value)
	{
		$values = $this->encoder->decode($value, self::ENCODE_MODE);
		$values =  json_decode($values, true);

		if (!is_array($values) && 
			!isset($values['value']) && 
			!isset($values['iv'])) {
			
			$ex = "Couldn't decrypt the given value";
			throw new EncryptionException($ex);
		}

		$iv = $this->encoder->decode($values['iv'], self::ENCODE_MODE);
		$value = $this->encoder->decode($values['value'], self::ENCODE_MODE);

		$value = openssl_decrypt($value, $this->cipher, $this->key, 0, $iv);
		
		return $value;
	}

	/**
	 * Get defined encryption key
	 * 
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Get defined encryption cipher
	 * 
	 * @return string
	 */	
	public function getCipher()
	{
		return $this->cipher;
	}
}
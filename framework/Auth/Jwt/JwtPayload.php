<?php
namespace Repository\Component\Auth\Jwt;

use DateTime;
use InvalidArgumentException;
use Repository\Component\Support\Encoder;

/**
 * Jwt Payload.
 *
 * @package	  \Repository\Component\Auth
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class JwtPayload
{
	/**
	 * Jwt payload claims
	 * @var array $claims
	 */
    private $claims = [
        'iss' => null,
        'sub' => null,
        'aud' => null,
        'exp' => null,
        'nbf' => null,
        'iat' => null,
        'jti' => null
    ];
    
    /**
     * JTI salt container
     * @var string $jtiSalt
     */
    private $jtiSalt = '';
    
    /**
     * Create jti salt for randomness JTI
     */
    public function __construct()
    {
    	$this->jtiSalt = $this->getJtiSalt();
    }

    /**
     * Encode defined JWT payload
     * 
     * @return string The encooded payload
     */
    public function encode()
    {
    	$payload = json_encode($this->getPayloads());
    	$payload = Encoder::base64Urlencode($payload);
    	
    	return $payload;
    }

    /**
     * Get payload by the given claim name
     * 
     * @param string $name
     * 
     * @return string|array The requested payload claim
     */
    public function get(string $name)
    {
    	if (array_key_exists($name, $this->claims)) {
    		return $this->claims['name'];
    	}
    }

    /**
     * Adds an extra claim
     *
     * @param string $name The name of the claim to add
     * @param mixed $value The value to add
     */
    public function add(string $name, $value)
    {
        if (in_array($name, ['exp', 'nbf', 'iat']) && is_int($value)) {
            $value = DateTime::createFromFormat('U', $value);
        }

        $this->claims[$name] = $value;
    }

    /**
     * Get resolved defined payloads
     * 
     * @return array
     */
    public function getPayloads()
    {
    	$payloads = array();
    	$dateTimes = array('iat', 'nbf', 'exp');
    	
    	foreach($this->claims as $claim => $value) {
    		if ($value !== null && in_array($claim, $dateTimes)) {
    			$value = $value->getTimestamp();
    		}
    		
    		$payloads[$claim] = $value;
    	}
    	
    	return $payloads;
    }

    /**
     * Set JWT issuer by the given issuer name
     * 
     * @param string $issuer
     * 
     * @return void
     */
    public function setIssuer(string $issuer)
    {
    	$this->claims['iss'] = $issuer;
    }

    /**
     * Set JWT subject by the given subject name
     * 
     * @param string $subject
     * 
     * @return void
     */
    public function setSubject(string $subject)
    {
    	$this->claims['sub'] = $subject;
    }

    /**
     * Set JWT audience by the given audience name
     * 
     * @param string $audience
     * 
     * @throw \Repository\Component\Authentication\Exception\JwtPayloadException
     * 
     * @return void
     */
    public function setAudience($audience)
    {
    	if (!is_array($audience) && !is_string($audience)) {
    		$ex = "The audience payload must be string or array";
    		throw new InvalidArgumentException($ex);
    	}

    	$this->claims['aud'] = $audience;
    }

    /**
     * Set JWT issued at by the given immutable issued time
     * 
     * @param \DateTime $time
     * 
     * @return void
     */
    public function setIssuedAt(DateTime $time)
    {
    	$this->claims['iat'] = $time;
    }

    /**
     * Set JWT valid from by the given immutable time
     * 
     * @param \DateTime $time
     * 
     * @return void
     */
    public function setValidFrom(DateTime $time)
    {
    	$this->claims['nbf'] = $time;
    }

    /**
     * Set JWT expired at by the given immutable expired time
     * 
     * @param \DateTime $time
     * 
     * @return void
     */
    public function setExpiredAt(DateTime $time)
    {
    	$this->claims['exp'] = $time;
    }

    /**
     * Set JWT id by the given id
     * 
     * @param string $id
     * 
     * @return void
     */
    public function setId(string $id)
    {
    	$this->claims['jti'] = $id;
    }

    /**
     * Get JWT issuer claim
     * 
     * @return string|null
     */
    public function getIssuer()
    {
        return $this->claims['iss'];
    }

    /**
     * Get JWT issued at time
     * 
     * @return DateTime|null
     */
    public function getIssuedAt()
    {
        return $this->claims['iat'];
    }

    /**
     * Get JWT subject claim
     * 
     * @return string|null
     */
    public function getSubject()
    {
        return $this->claims['sub'];
    }

    /**
     * Get JWT token audiences
     * 
     * @return string|array
     */
    public function getAudience()
    {
        return $this->claims['aud'];
    }

    /**
     * Get JWT token valid from
     * 
     * @return DateTime|null
     */
    public function getValidFrom()
    {
        return $this->claims['nbf'];
    }

    /**
     * Get expired JWT token
     * 
     * @return DateTime|null
     */
    public function getExpiredAt()
    {
        return $this->claims['exp'];
    }

    /**
     * Get expired JWT token
     * 
     * @return string
     */
    public function getId()
    {
    	if (isset($this->claims['jti'])) {
    		return $this->claims['jti'];
    	}
    	
    	$claims = $this->claims['jti'];
    	$salt = $this->getJtiSalt();

    	return md5(json_encode($claims) . $salt);
    }

    /**
     * Get jwt payload claims
     * 
     * @return array
     */
	public function getClaims()
	{
		return $this->claims;
	}

    /**
     * Generate JTI salt
     * 
     * @return DateTime|null
     */
    private function getJtiSalt()
    {
    	$salt = bin2hex(random_bytes(8));
    	
    	return $salt;
    }
}
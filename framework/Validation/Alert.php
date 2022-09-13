<?php
namespace Repository\Component\Validation;

use Repository\Component\Http\Request;
use Repository\Component\Session\Session;
use Repository\Component\Collection\Collection;
use Repository\Component\Contracts\Hashing\HashInterface;
use Repository\Component\Validation\Exception\AlertException;
use Repository\Component\Contracts\Container\ContainerInterface;

/**
 * Alert.
 * 
 * @package	  \Repository\Component\Validation
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Alert
{
	/**
	 * Validation alerts container
	 * @var array $validationAlerts
	 */
	protected $validationAlerts = array();

	/**
	 * Validation alert indicator
	 * @var array $indicators
	 */
	protected $indicators = array();

	/**
	 * Requested form fields
	 * @var array $fields
	 */	
	protected $fields = array();

	/**
	 * Container instance
	 * @var \Repository\Component\Contracts\Container\ContainerInterface $app
	 */	
	protected $app;

	/**
	 * Hash instance
	 * @var \Repository\Component\Hashing\Hash $hash
	 */	
	protected $hash;

	/**
	 * Validation rule instance
	 * @var \Repository\Component\Validation\Rule $rule
	 */	
	protected $rule;

	/**
	 * UploadedFile instance
	 * @var \Repository\Component\Validation\UploadedFile $uploadedFile
	 */	
	protected $uploadedFile;
	
	/**
	 * @inheritdoc
	 * @param \Repository\Component\Contracts\Container\ContainerInterface $app
	 * @param \Repository\Component\Validation\Rule
	 * @param \Repository\Component\Hashing\Hash $hash
	 */
	public function __construct(
		ContainerInterface $app, 
		Rule $rule, 
		HashInterface $hash)
	{
		$this->app = $app;
		$this->hash = $hash;
		$this->rule = $rule;
		
		$uploadedFile = new UploadedFile($this->rule, $this);
		$this->uploadedFile = $uploadedFile;
	}

	/**
	 * Initialize validation by the given patterns
	 * 
	 * @param array $patterns Validation patterns
	 * 
	 * @return void
	 */
	public function make(array $patterns)
	{
		//Map any passed validation patterns and validate it
		array_map (function($field, $pattern) {
			//Set request form field to the cache container
			$this->setRequestField($field);
			
			if (!$this->app['request']->has($field) && !isset($_FILES[$field])) {
				$this->resolveRequired($field, $pattern);
				$this->resolveRequiredIf($field, $pattern);
			}

			//Remove pipe character from the beginning and last pattern
			$pattern = trim($pattern, Validation::PIPE);
			//If content is formed as array
			//It's means that content is uploaded field
			//So what we can do is just checking that uploaded field
			//is not empty and validate the uploaded file		
			if ($field  === Validation::$uploadedField && is_array($this->get($field))) {
				$this->resolveRequired($field, $pattern);
				$this->resolveRequiredIf($field, $pattern);
				$this->resolveUploadedFile($field);
				//Return it, 
				//so we won't have unexpected validation
				return;
			}
			
			//Get minimal and maximal patterns			
			$minMaxPatterns = $this->getMinMaxPattern($field, $pattern);

			foreach($minMaxPatterns as $index => $minmaxs) {
				//Resolve minimal and maximal boundary by the given pattern
				//Important to matching min max pattern with the current field
				//So we always have an appropriate min max validation
				if ($index === $field) {
					$this->resolveMinMaxFilteration($minmaxs);
				}
			}

			//Resolve equal validation			
			$this->resolveEqualFilteration($field, $pattern);
			//Resolve required validation
			$this->resolveRequired($field, $pattern);
			//Resolve required if validation
			$this->resolveRequiredIf($field, $pattern);
			//Resolve required if validation
			$this->resolveRequiredIfNotSigned($field, $pattern);
			//Resolve json validation
			$this->validateJson($field, $pattern);	
			//Resolve datetime validation
			$this->validateDateTime($field, $pattern);	
			//Resolve string validation
			$this->validateString($field, $pattern);	
			//Resolve boolean validation
			$this->validateBoolean($field, $pattern);	
			//Resolve integer validation
			$this->validateInteger($field, $pattern);
			//Resolve numeric validation
			$this->validateNumeric($field, $pattern);
			//Resolve between validation
			$this->validateBetween($field, $pattern);
			//Resolve url validation
			$this->validateUrl($field, $pattern);
			//Resolve domain validation
			$this->validateDomain($field, $pattern);
			//Resolve file validation
			$this->validateFileExist($field, $pattern);
			//Resolve validation by the regular pattern
			$this->resolveGeneralPattern($field, $pattern);
		}, array_keys($patterns), $patterns);
	}

	/**
	 * Resolve uploaded file validation
	 * The validation will automatically upload the requested field
	 * 
	 * @param string $field Form field
	 * 
	 * @return void
	 */		
	public function resolveUploadedFile($field)
	{
		//Build uploaded file validation instance
		$uploadedFile = $this->uploadedFile;
		//If field equal with default uploaded field
		//We can validate it
		if ($field === Validation::$uploadedField) {
			$uploadedFile->initialize($this->get($field));
			$uploadedFile->resolveUploadedFile();
		}
	}

	/**
	 * Fetch all requested form fields
	 * 
	 * @return array
	 */
	public function allRequest()
	{
		$requests = array();
		$fields = $this->getRequestFields();
		$collection = Collection::make($fields);
		$sources = $this->app['request']->getInputSource();

		$collection->map(function($field) use (&$requests, $sources){
			$passwordField = Validation::$passwordField;
			//When form field equal with uploaded field
			//Ignore it, we won't capture it as a results
			if ($field === Validation::$uploadedField) return;
			//If field form come to password field
			//We hash it automatically
			if ($field === $passwordField) {
				$password = isset($sources[$passwordField]) ? $sources[$passwordField] : null;
				$requests[$field] = $password !== null ? $this->hash->make($password) : null;
			} else {
				$requests[$field] = isset($sources[$field]) ? $sources[$field] : null;
			}
			
			return $requests;
		});

		return $requests;
	}

	/**
	 * Resolve minimal and maximal boundary based on indication
	 * 
	 * @param array $minmaxs Minimal and maximal boundaries
	 *  
	 * @return void
	 */
	public function resolveMinMaxFilteration(array $minmaxs)
	{
		//Make an items collection
		$collection = Collection::make($minmaxs);
		
		//Determine if the collection is empty and return it
		if ($collection->isEmpty()) return;

		switch (true) {
			//When item has min index and doesn't have max index
			case $collection->has('min') && !$collection->has('max'):

				$minimal = $collection->get('min');				
				$this->resolveMinimalIndication(
					$minimal['field'], 
					$minimal['length']
				);

			break;
			//When item doesn't have min index and has max index
			case !$collection->has('min') && $collection->has('max'):

				$maximal = $collection->get('max');				
				$this->resolveMaximalIndication(
					$maximal['field'], 
					$maximal['length']
				);

			break;
			//When items has both min and max index
			case $collection->has('min') && $collection->has('max'):

				$minimal = $collection->get('min');
				$maximal = $collection->get('max');
				//Cause we are in same level field
				//that want validate, we can just use minimal field
				//to the first method parameter
				$this->resolveMinMaxIndication(
					$minimal['field'], 
					$minimal['length'], 
					$maximal['length']
				);

			break;
		}
	}
	
	/**
	 * Resolve minimal and maximal boundary indication
	 * 
	 * @param string $field Form field
	 * @param int $minimal Minimal boundary
	 * @param int $maximal Maximal boundary
	 *  
	 * @return void
	 */
	public function resolveMinMaxIndication($field, int $minimal, int $maximal)
	{
		//Fetch alert messages from the rule container
		$alerts = $this->rule->getAlertMessages();
		//Fetch expected content from the PHP stream
		$content = $this->get($field);
		
		if (!is_string($content) || is_integer($content)) {
			return;
		}
		
		$content = Filter::make($content);
		//Determine minimal and maximal indicator
		$isMinimal  = $content->isGreaterThanEqual($minimal);
		$isMaximal  = $content->isLessThanEqual($maximal);

		if ($isMinimal && $isMaximal) {
			//Set minimal and maximal alert indicator to true
			//So we can know that validation passed oke
			$this->setAlertIndicator($field, true);
			
			return;
		}

		//By the sprintf we can change alert message dynamically
		$alert = sprintf($alerts['minmax'], $field, $minimal, $maximal);
		//Set alert message to the list
		//So we can know that validation failure passed
		$this->setValidationAlert($field, $alert);
		//Again, set minimal and maximal alert indicator to false
		$this->setAlertIndicator($field, false);
	}

	/**
	 * Validate json data
	 * 
	 * @param string $field Form field
	 * @param string $pattern Validation pattern
	 * 
	 * @return void
	 */
	public function validateJson($field, $pattern)
	{
		//Get the content by the given field
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		//Explode pattern by pipe `|` character
		$patterns = explode(Validation::PIPE, $pattern);
		$collection = Collection::make($patterns);

		array_walk($patterns, function ($pattern) use ($field, $content, $alerts) {
			if (mb_strpos($pattern, 'json') !== false && $content !== '') {
				if (!is_string($content)) {
					$this->setValidationAlert($field, $alerts['json']);
					$this->setAlertIndicator($field, false);

					return;
				}

				$json = json_decode($content);

				if (!(json_last_error() === JSON_ERROR_NONE)) {
					//Set alert message to the alert container
					$this->setValidationAlert($field, $alerts['json']);
					$this->setAlertIndicator($field, false);
				}
				
				if (mb_strpos($pattern, 'json:') !== false) {
					$pattern = str_replace('json:', '', $pattern);
					$parts = explode('.', $pattern);

					array_walk($parts, function ($part) use ($field, $json, $alerts) {
						if ($part !== '') {
							if (is_array($json)) {
								for ($i = 0; $i < count($json); $i++) {
									if (!isset($json[$i]->{$part})) {
										$this->setValidationAlert($field, $alerts['json']);
										$this->setAlertIndicator($field, false);
									}
								}

								if (!$json) {
									$this->setValidationAlert($field, $alerts['json']);
									$this->setAlertIndicator($field, false);
								}
							} else {
								if (!isset($json->{$part})) {
									$this->setValidationAlert($field, $alerts['json']);
									$this->setAlertIndicator($field, false);
								}
							}
						}
					});
				}
			}
		});
	}

	public function validateBetween($field, $pattern)
	{
		$alerts = $this->rule->getAlertMessages();

		$content = $this->get($field);
		$content = is_bool($content) ? var_export(boolval($content), true) : $content;

		$patterns = explode(Validation::PIPE, $pattern);
		$collection = Collection::make($patterns);

		array_walk($patterns, function ($pattern) use ($field, $content, $alerts) {
			if (mb_strpos($pattern, 'between:') !== false && $content !== '') {
				$pattern = str_replace('between:', '', $pattern);
				$parts = explode(',', $pattern);

				if (!in_array($content, $parts)) {
					$lastOption = array_pop($parts);

					$option = implode(',', $parts);

					$option = sprintf('%s %s %s', $option, self::__('OR'), $lastOption);

					$alert = sprintf($alerts['between'], self::__($field), $option);

					$this->setValidationAlert($field, $alert);
					$this->setAlertIndicator($field, false);
				}
			}
		});
	}

	public function validateDateTime($field, $pattern)
	{
		//Get the content by the given field
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		//Explode pattern by pipe `|` character
		$patterns = explode(Validation::PIPE, $pattern);

		array_filter($patterns, function ($pattern) use ($field, $content, $alerts) {
			if (mb_strpos($pattern, 'datetime:') !== false) {
				$parts = explode('datetime:', $pattern);

				$parts = array_filter($parts, function ($part) use ($field, $content, $alerts) {
					return $part !== '';
				});

				$format = array_shift($parts);

				if ($content !== '') {
					if (!$this->doValidateDateTime($content, $format)) {
						$this->setValidationAlert($field, $alerts['datetime']);
						$this->setAlertIndicator($field, false);
					}
				}
			}
		});
	}

	public function validateFileExist($field, $pattern)
	{
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		$patterns = explode(Validation::PIPE, $pattern);

		$collection = Collection::make($patterns);
		
		if ($collection->contains('file_exist')) {
			if (!file_exists($content) && $content !== '') {
				$this->setValidationAlert($field, $alerts['file_exist']);
				$this->setAlertIndicator($field, false);
			}
		}
	}

	public function validateString($field, $pattern)
	{
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		$patterns = explode(Validation::PIPE, $pattern);

		$collection = Collection::make($patterns);
		
		if ($collection->contains('string')) {
			if (!is_string($content) && $content !== '') {
				$this->setValidationAlert($field, $alerts['string']);
				$this->setAlertIndicator($field, false);
			}
		}
	}

	public function validateBoolean($field, $pattern)
	{
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		$patterns = explode(Validation::PIPE, $pattern);

		$collection = Collection::make($patterns);

		if ($collection->contains('bool')) {
			if (!in_array($content, array('true', 'false', '0', '1')) && $content !== '') {
				$this->setValidationAlert($field, $alerts['bool']);
				$this->setAlertIndicator($field, false);
			}
		}
	}

	public function validateInteger($field, $pattern)
	{
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		$patterns = explode(Validation::PIPE, $pattern);

		$collection = Collection::make($patterns);

		if ($collection->contains('integer')) {
			if (!is_int($content) && $content !== '') {
				$this->setValidationAlert($field, $alerts['integer']);
				$this->setAlertIndicator($field, false);
			}
		}
	}

	public function validateNumeric($field, $pattern)
	{
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		$patterns = explode(Validation::PIPE, $pattern);

		$collection = Collection::make($patterns);

		if ($collection->contains('numeric')) {
			if (!is_numeric($content) && $content !== '') {
				$this->setValidationAlert($field, $alerts['numeric']);
				$this->setAlertIndicator($field, false);
			}
		}
	}

	public function validateUrl($field, $pattern)
	{
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		$patterns = explode(Validation::PIPE, $pattern);

		$collection = Collection::make($patterns);

		if ($collection->contains('url')) {
			if (!filter_var($content, FILTER_VALIDATE_URL) && $content !== '') {
				$this->setValidationAlert($field, $alerts['url']);
				$this->setAlertIndicator($field, false);
			}
		}
	}

	public function doValidateDateTime(string $date, string $format, bool $strict = true)
	{
		$dt = \DateTime::createFromFormat($format, $date);

		if ($strict) {
			$errors = \DateTime::getLastErrors();

			if (!empty($errors['warning_count'])) {
				return false;
			}
		}

		return $dt !== false;
	}

	public function validateDomain($field, $pattern)
	{
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		$patterns = explode(Validation::PIPE, $pattern);

		$collection = Collection::make($patterns);

		if ($collection->contains('domain')) {
			if (!$this->validateDomainName($content) && $content !== '') {
				$this->setValidationAlert($field, $alerts['domain']);
				$this->setAlertIndicator($field, false);
			}
		}
	}

	/**
     * Validate if a domain name is valid
     * 
     * @param  string $domain_name
     * 
     * @return bool              
     * 
     */
    private function validateDomainName($domain_name)
    {
        //FILTER_VALIDATE_URL checks length but..why not? so we dont move forward with more expensive operations
        $domain_len = strlen($domain_name);

        if ($domain_len < 3 OR $domain_len > 253)
            return false;

        //getting rid of HTTP/S just in case was passed.
        if (stripos($domain_name, 'http://') === 0) {
            $domain_name = substr($domain_name, 7); 
        } elseif(stripos($domain_name, 'https://') === 0) {
            $domain_name = substr($domain_name, 8);
        }
        
        //we dont need the www either                 
        if (stripos($domain_name, 'www.') === 0) {
            $domain_name = substr($domain_name, 4); 
        }

        //Checking for a '.' at least, not in the beginning nor end, 
        //since http://.abcd. is reported valid
        if (strpos($domain_name, '.') === false or $domain_name[strlen($domain_name)-1]=='.' or $domain_name[0]=='.') {
            return false;
        }
                 
        //now we use the FILTER_VALIDATE_URL, concatenating http so we can use it, and return BOOL
        return (filter_var ('http://' . $domain_name, FILTER_VALIDATE_URL)===false)? false:true;

    }

	/**
	 * Resolve minimal boundary indication
	 * 
	 * @param string $field Form field
	 * @param int $length The length of minimal boundary
	 *  
	 * @return void
	 */
	public function resolveMinimalIndication($field, int $length)
	{
		//Fetch alert messages from the rule container
		$alerts = $this->rule->getAlertMessages();
		//Fetch expected content from the PHP stream
		$content = $this->get($field);

		if (!is_string($content) || is_integer($content)) {
			return;
		}

		//Determine minimal indicator
		$minimal  = Filter::make($content)->isLessThanEqual($length);

		if (!$minimal || $content === '') {
			//Set minimal and maximal alert indicator to true
			//So we can know that validation passed oke
			$this->setAlertIndicator($field, true);
			
			return;
		}
		
		//By the sprintf we can change alert message dynamically
		$alert = sprintf($alerts['minimal'], self::__($field), $length);
		//Set alert message to the list
		//So we can know that validation failure passed
		$this->setValidationAlert($field, $alert);
		//Again, set minimal and maximal alert indicator to false
		$this->setAlertIndicator($field, false);
	}

	/**
	 * Resolve maximal boundary indication
	 * 
	 * @param string $field Form field
	 * @param int $length The length of maximal boundary
	 *  
	 * @return void
	 */
	public function resolveMaximalIndication($field, int $length)
	{
		//Fetch alert messages from the rule container
		$alerts = $this->rule->getAlertMessages();
		//Fetch expected content from the PHP stream
		$content = $this->get($field);

		if (!is_string($content) || is_integer($content)) {
			return;
		}

		//Determine maximal indicator
		$maximal  = Filter::make($content)->isLessThanEqual($length);		

		if ($maximal || $content === '') {
			//Set minimal and maximal alert indicator to true
			//So we can know that validation passed oke
			$this->setAlertIndicator($field, true);
			
			return;
		}
		
		//By the sprintf we can change alert message dynamically
		$alert = sprintf($alerts['maximal'], self::__($field), $length);
		//Set alert message to the list
		//So we can know that validation failure passed
		$this->setValidationAlert($field, $alert);
		//Again, set minimal and maximal alert indicator to false
		$this->setAlertIndicator($field, false);
	}

	/**
	 * Get minimal and maximal validation pattern
	 * 
	 * @param string $field Form field
	 * @param string $patterns Validation pattern
	 * 
	 * @return void
	 */		
	public function getMinMaxPattern($field, $pattern)
	{
		$minmaxs = array();
		$minimal = "/(min\:{1}[0-9]+)/i";
		$maximal = "/(max\:{1}[0-9]+)/i";
		
		//Resolve minimal pattern
		if (preg_match($minimal, $pattern, $matches)):
			$minimal = str_replace(Validation::MIN, '', $matches[0]);
			$minmaxs[$field]['min']['field'] = $field;
			$minmaxs[$field]['min']['length'] = $minimal;
			$minmaxs[$field]['min']['content'] = $this->get($field);
		endif;
		
		//Resolve maximal pattern
		if (preg_match($maximal, $pattern, $matches)):
			$maximal = str_replace(Validation::MAX, '', $matches[0]);
			$minmaxs[$field]['max']['field'] = $field;
			$minmaxs[$field]['max']['length'] = $maximal;
			$minmaxs[$field]['max']['content'] = $this->get($field);
		endif;

		return $minmaxs;
	}

	/**
	 * Resolve equalidity validation pattern
	 * 
	 * @param string $field Form field
	 * @param string $pattern Validation pattern
	 * 
	 * @return void
	 */			
	public function resolveEqualFilteration($field, $pattern)
	{
		//Resolve equal sign from passed pattern
		$match = "/(equal\:{1}[-_a-zA-Z0-9]+)/i";

		if (preg_match($match, $pattern, $matches)) {
			//Remove equal sign and get the equal field indication
			$indication = str_replace(Validation::EQUAL, '', $matches[0]);
			//Resolve equal indication by the given indication
			$this->resolveEqualIndication($field, $indication);
		}
	}

	/**
	 * Resolve equal indication by the given field and expected field
	 * 
	 * @param string $field Form field
	 * @param string $expected Expected form field
	 * 
	 * @return void
	 */		
	public function resolveEqualIndication($field, $expected)
	{
		//Fetch alert messages from the rule container
		$alerts = $this->rule->getAlertMessages();
		//Fetch expected content from the PHP stream
		$content = $this->get($field);
		//Expected content that want compared
		$expectedContent = $this->get($expected);
		//Determine equal indicator
		$isMatch = Filter::make($content)->isEqualWith($expectedContent);

		if ($isMatch) {
			//Set minimal and maximal alert indicator to true
			//So we can know that validation passed oke
			$this->setAlertIndicator($field, true);
			
			return;
		}
		
		//By the sprintf we can change alert message dynamically
		$alert = sprintf($alerts['equal'], self::__($field), self::__($expected));
		//Set alert message to the list
		//So we can know that validation failure passed
		$this->setValidationAlert($field, $alert);
		//Again, set minimal and maximal alert indicator to false
		$this->setAlertIndicator($field, false);
	}

	protected function __(string $context)
	{
		if (function_exists('__')) {
			return __(mb_strtoupper($context));
		}

		return $context;
	}

	/**
	 * Resolve general validation pattern separated with pipe caharacter `|`
	 * 
	 * @param string $field Form field
	 * @param string $patterns Validation pattern
	 * 
	 * @return void
	 */	
	public function resolveGeneralPattern($field, $pattern)
	{
		//Get the content by the given field
		$content = $this->get($field);
		//Explode pattern by pipe `|` character
		$patterns = explode(Validation::PIPE, $pattern);
		//Resolve general pattern
		//letter, email, birthday, etc...
		array_map (function($pattern) use ($field, $content) {
			$patterns = Collection::make((array) $pattern);
			//Remove `required` pattern from pattern container
			$patterns = array_filter($patterns->all(), function($pattern) {
				return 'required' !== $pattern;
			});

			//Collect the filetered pattern items
			$patterns = Collection::make($patterns);
			//Validate request when requested content doesn't empty
			//and patterns contains expected pattern
			if (!empty($content) && $patterns->contains($pattern)) {
				//Get validation rule by the given pattern
				$validationRule = $this->rule->getRule($pattern);
				
				if (!empty($validationRule)) {
					if (!preg_match($validationRule, $content)) {
						if (array_key_exists($pattern, $this->rule->getAlertMessages())) {
							$this->setValidationAlert($field, $this->rule->getAlertMessage($pattern));
							$this->setAlertIndicator($field, false);
						}
					}
				}
			}
		}, $patterns);
	}

	/**
	 * Resolve required validation pattern
	 * 
	 * @param string $field Form field
	 * @param string $patterns Validation pattern
	 * 
	 * @return void
	 */	
	public function resolveRequired($field, $pattern)
	{
		//Get the content by the given field
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		//Explode pattern by pipe `|` character
		$patterns = explode(Validation::PIPE, $pattern);
		$collection = Collection::make($patterns);

		if ($collection->contains('required') && $content === '') {
			//Set alert message to the alert container
			$this->setValidationAlert($field, $alerts['required']);
			$this->setAlertIndicator($field, false);
		}
	}

	/**
	 * Resolve required validation if user  unsigned pattern
	 * 
	 * @param string $field Form field
	 * @param string $patterns Validation pattern
	 * 
	 * @return void
	 */	
	public function resolveRequiredIfNotSigned($field, $pattern)
	{
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		$patterns = explode(Validation::PIPE, $pattern);

		$collection = Collection::make($patterns);

		if ($collection->contains('requiredIfNotSigned')) {
			$session = $this->app['session'];

			if (!$session->has(SIGNED_CREDENTIAL) && $content === '') {
				$this->setValidationAlert($field, $alerts['required']);
				$this->setAlertIndicator($field, false);
			}
		}
	}

	/**
	 * Resolve required if condition validation pattern
	 * 
	 * @param string $field Form field
	 * @param string $patterns Validation pattern
	 * 
	 * @return void
	 */	
	public function resolveRequiredIf($field, $pattern)
	{
		//Get the content by the given field
		$content = $this->get($field);
		$alerts = $this->rule->getAlertMessages();
		//Explode pattern by pipe `|` character
		$patterns = explode(Validation::PIPE, $pattern);

		array_walk($patterns, function ($pattern) use ($field, $content, $alerts) {
			if (mb_strpos($pattern, 'requiredIf') !== false) {
				$pattern = str_replace('requiredIf:', '', $pattern);
				$pairs = explode('&', $pattern);
				$pairs = explode('=', implode('=', $pairs));

				if (count($pairs) === 2) {
					$value = $this->get($pairs[0]);
					$value = is_bool($value) ? var_export(boolval($value), true) : $value;

					if ($value === $pairs[1]) {
						if ($content === '') {
							$this->setValidationAlert($field, $alerts['required']);
							$this->setAlertIndicator($field, false);
						}
					}
				}

				if (count($pairs) === 4) {
					$value1 = $this->get($pairs[0]);
					$value1 = is_bool($value1) ? var_export(boolval($value1), true) : $value1;

					$value2 = $this->get($pairs[0]);
					$value2 = is_bool($value2) ? var_export(boolval($value2), true) : $value2;

					if ($value1 === $pairs[1] && $value2 === $pairs[3]) {
						if ($content === '') {
							$this->setValidationAlert($field, $alerts['required']);
							$this->setAlertIndicator($field, false);
						}
					}
				}
			}
		});
	}

	/**
	 * Fetch http safe method: 'POST', 'PUT', 'DELETE'
	 * 
	 * @return array
	 */
	public function getHttpSafeMethods()
	{
		//Flip http methods value with index
		$httpMethods = array_flip(Request::$httpMethods);
		
		//Nice, we just need to unset by the http method
		unset($httpMethods['get']);
		unset($httpMethods['head']);
		unset($httpMethods['patch']);
		unset($httpMethods['options']);
		
		return array_flip($httpMethods);
	}

	/**
	 * Fetch content by the given field
	 * 
	 * @param string $field Form field
	 *  
	 * @return string|array
	 * 
	 * @throw \Repository\Component\Validation\Exception\AlertException
	 */
	public function get($field)
	{
		//Fetch http safe methods
		$httpMethods = $this->getHttpSafeMethods();
		$httpMethods = Collection::make($httpMethods);
		$requestMethod = $this->app['request']->getRequestMethod();
		//Fetch all requests
		$sources = (array) $this->app['request']->getInputSource();

		if (empty($sources) || $sources === "") {
			$sources = array();
		}
		
		if ($httpMethods->contains($requestMethod)) {
			$uploadedField = Validation::$uploadedField;

			if ($field === $uploadedField) {
				if (isset($_FILES[$uploadedField])) {
					return $_FILES[$uploadedField];
				}
	  		}
			
	  		return isset($sources[$field]) ? $sources[$field] : '';
		}

		return isset($sources[$field]) ? $sources[$field] : '';
	}

	/**
	 * Get alert message by the given key
	 * 
	 * @param string $key
	 * 
	 * @return string Alert message
	 */		
	public function getMessage($key)
	{
		$alerts = $this->rule->getAlertMessages();
		
		return $alerts[$key];
	}

	/**
	 * Set paired validation alert indicator
	 * 
	 * @param string|bool|null $key Form field
	 * @param string|bool|null $condition
	 *  
	 * @return void
	 */
	public function setAlertIndicator($key, $condition)
	{
		if (null === $key) {
			$this->indicators[] = $condition;
		}
		
		$this->indicators[$key] = $condition;
	}
	
	/**
	 * Get alert indicators
	 *  
	 * @return array
	 */	
	public function getAlertIndicators()
	{
		return $this->indicators;
	}

	/**
	 * Set paired validation alert
	 * 
	 * @param string $key Form field
	 * @param string $alert Alert message
	 *  
	 * @return void
	 */
	public function setValidationAlert($key, $alert)
	{
		$this->validationAlerts[$key] = $alert;
	}

	/**
	 * Get validation alert by the given key
	 * 
	 * @param string $key Form field
	 *  
	 * @return string An alert that indicating current error of the field
	 * Return null otherwise
	 */	
	public function getValidationAlert($key)
	{
		if (!isset($this->validationAlerts[$key]))
			return;

		$alert = $this->validationAlerts[$key];
		
		return $alert;
	}
	
	/**
	 * Get validation alert by the given key
	 * 
	 * @param string $key Form field
	 *  
	 * @return string An alert that indicating current error of the field
	 * Return null otherwise
	 */	
	public function alerts()
	{
		return $this->validationAlerts;
	}

	/**
	 * Get validation alert by the given key
	 * 
	 * @param string $key Form field
	 *  
	 * @return string An alert that indicating current error of the field
	 * Return null otherwise
	 */	
	public function alert($key)
	{
		return $this->getValidationAlert($key);
	}
	
	public function getUploadedFileInstance()
	{
		$uploadedFile = $this->uploadedFile;
		
		return $uploadedFile;
	}

	/**
	 * Set request form field by the given key
	 * 
	 * @param string $key Field form
	 * 
	 * @return \Repository\Component\Validation\Alert
	 */
	public function setRequestField($key)
	{
		$this->fields[] = $key;
		
		return $this;
	}

	/**
	 * Get request form fields
	 * 
	 * @return array
	 */	
	public function getRequestFields()
	{
		$fields = $this->fields;
		
		return $fields;
	}
}
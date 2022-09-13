<?php
namespace Repository\Component\Language;

use Symfony\Component\Yaml\Yaml;

/**
 * Internationalization (i18n) class. Provides language loading and translation
 * methods without dependencies on [gettext](http://php.net/gettext).
 *
 * Loads the message and replaces parameters:
 *
 *     // Display a translated message
 *     echo __('Hello, world');
 *
 *     // With parameter replacement
 *     echo __('Hello, :user', array(':user' => $username));
 */
class Lang
{
	/**
     * The target language: en-us, es-es, zh-cn, etc
     * 
	 * @var string $lang
	 */
	public static $lang = 'en-us';

	/**
     * The target language: en-us, es-es, zh-cn, etc
     * 
	 * @var array $basepaths
	 */
	public static $basepaths = array();

	/**
	 * The source language: en-us, es-es, zh-cn, etc
     * 
     * @var string $source
	 */
	public static $source = 'en-us';

	/**
     * The cache of loaded languages
	 * @var array $caches
	 */
	protected static $caches = array();

	/**
	 * Get and set the target language.
	 *
	 *     // Get the current language
	 *     $lang = Lang::lang();
	 *
	 *     // Change the current language to Spanish
	 *     Lang::lang('es-es');
	 *
	 * @param string $lang The new language setting
     * 
	 * @return string
	 */
	public static function lang($lang = null)
	{
		if ($lang) {
			// Normalize the language
			Lang::$lang = mb_strtolower(str_replace(array(' ', '_'), '-', $lang));
		}

		return Lang::$lang;
	}

	/**
	 * Set the target language translation basepath.
	 *
	 * @param string $lang The language translation basepath
     * 
	 * @return null
	 */
	public static function basepath(string $basepath)
	{
		Lang::$basepaths[] = $basepath;
	}

	/**
	 * Returns translation of a string. If no translation exists, the original
	 * string will be returned. No parameters are replaced.
	 *
	 *     $hello = Lang::get('Hello friends, my name is :name');
	 *
	 * @param string $string The text to translate
	 * @param string $lang The target language
     * 
	 * @return string
	 */
	public static function get($string, $lang = null)
	{
		if (!$lang) {
			// Use the global target language
			$lang = Lang::$lang;
		}
		
		// Load the translation table for this language
		$table = Lang::load($lang);

		// Return the translated string if it exists
		$value = isset($table[$string]) ? $table[$string] : $string;

		$value = Lang::interpolate($value);

		return $value;
	}

	public static function getAll($lang = null)
	{
		if (!$lang) {
			// Use the global target language
			$lang = Lang::$lang;
		}
		
		// Load the translation table for this language
		$tables = Lang::load($lang);

		foreach ($tables as $key => $value) {
			$tables[$key] = lang::interpolate($value);
		}

		return $tables;
	}

	/**
	 * Get languages translation by the given path language.
	 *
	 * @param string $path The path where's the language exist
	 * 
	 * @return array
	 */
	private static function getLanguages(string $path)
	{
		if (file_exists($yaml = $path . '.yaml' )) {
			return (array) Yaml::parse(file_get_contents($yaml));
		}

		if (file_exists($php = $path . '.php' )) {
			return (array) require $php;
		}

		return array();
	}


	/**
	 * Interpolate message with .env configuration.
	 *
	 * @param string $message The message to be interpolate
	 * @param string $contexts The optional contexts
	 * 
	 * @return array
	 */
	public static function interpolate(&$message, array $contexts = array())
	{
		$localeContexts = array();
		
		if (preg_match_all("/\{([a-zA-Z0-9\_]+)\}/is", $message, $matches)) {
			if (isset($matches[1])) {
				foreach ($matches[1] as $key => $context) {

					if (function_exists('app_env') && app_env($context) !== null) {
						$context = app_env($context);
					}

					$localeContexts[$matches[0][$key]] = $context;
				}
			}
		}

		$contexts = array_merge($localeContexts, $contexts);

		return Lang::doInterpolate($message, $contexts);
	}

	/**
	 * Interpolate message with the translation.
	 *
	 * @param string $message The message to be interpolate
	 * @param string $contexts The optional contexts
	 * 
	 * @return array
	 */
	public static function doInterpolate(&$message, array $context = array())
	{
		$items = array();

		foreach ($context as $index => $item) {
			if (is_string($item)) {
				$items[$index] = Lang::get($item, Lang::$lang);
			}
		}
		
		return strtr($message, $items);
	}

	/**
	 * Returns the translation table for a given language.
	 *
	 *     // Get all defined Spanish messages
	 *     $messages = Load::load('es-es');
	 *
	 * @param string $lang The language to load
	 * 
	 * @return array
	 */
	public static function load($lang)
	{
		if (isset(Lang::$caches[$lang])) {
			return Lang::$caches[$lang];
		}

		// Split the language: language, region, locale, etc
		$parts = explode('-', $lang);

		// Create a path for this set of parts
		$path = implode(DS, $parts);
		
		Lang::$basepaths = array_unique(Lang::$basepaths);
		
		$tables = array();
		
		foreach (Lang::$basepaths as $basepath) {
			$target = $basepath . DS . $path;

			$tables += self::getLanguages($target);
		}

		// Cache the translation table locally
		return Lang::$caches[$lang] = $tables;
	}
}
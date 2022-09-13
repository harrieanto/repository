<?php

use Repository\Component\Support\Str;
use Repository\Component\Config\Config;
use Repository\Component\Language\Lang;
use Repository\Component\View\ViewFactory;
use Repository\Component\Routing\UrlGenerator;
use Repository\Component\Foundation\Application as App;

/**
 * Framework Helpers.
 * 
 * @package	  \Repository\Component\Support
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
if (!function_exists('app_env')) {
	function app_env($group, $newValue = '') {
		$environments = Config::get('env');

		if (!isset($environments[$group])) {
			return null;
		}

		if (!empty($newValue)) {
			Config::get('env')[$group] = $newValue;
		}

		return Config::get('env')[$group];
	}
}

if (!function_exists('app')) {
	function app() {
		return App::getInstance();
	}
}

if (!function_exists('route')) {
	function route($alias, ...$defaultValues) {
		$generator = new UrlGenerator(app()['route']);
		return $generator->route($alias, ...$defaultValues);
	}
}

if (!function_exists('route_path')) {
	function route_path($alias, ...$values) {
		$parts = parse_url(route($alias, ...$values));
		return isset($parts['path']) ? $parts['path'] : null;
	}
}

if (!function_exists('app_config')) {
	function app_config($group) {
		return Config::get($group);
	}
}

if (!function_exists('dd')) {
	function dd($payload) {
		var_dump($payload);
		die();
	}
}

if (!function_exists('rview')) {
	function rview($targets, $variables = null, $contents = null) {
		$view = ViewFactory::create();
		
		if (is_array($targets)) {
			foreach($targets as $dirName => $fileName) {
				$view->setTargetBasepath($dirName);

				if ($variables && $contents !== null) {
					$view->make($fileName)->with($variables, $contents);
					return;
				}

				$view->to($fileName);
				return;
			}
		}

		if ($variables && $contents !== null) {
			$view->make($targets)->with($variables, $contents);
			return;
		}

		$view->to($targets);
	}
}

if ( ! function_exists('__')) {
	/**
	 * I18n translation/internationalization function. The PHP function
	 * [strtr](http://php.net/strtr) is used for replacing parameters.
	 *
	 *    __('Welcome back, :user', array(':user' => $username));
	 *
	 * [!!] The target language is defined by [Lang::$lang].
	 * 
	 * @uses Lang::get
	 * 
	 * @param string $string The text to translate
	 * @param array $values The values to replace in the translated text
	 * @param string $lang The source language
     * 
	 * @return string
	 */
	function __($string, array $values = null, $lang = null)
	{
		$lang = ($lang !== null) ? $lang : Lang::$lang;

		$string = Lang::get($string, $lang);

		return empty($values) ? $string : strtr($string, $values);
	}
}
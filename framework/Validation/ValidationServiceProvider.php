<?php
namespace Repository\Component\Validation;

use App\Http\Request\Request;
use Repository\Component\Hashing\Hash;
use Repository\Component\Support\ServiceProvider;

/**
 * Validation Service Provider.
 * 
 * @package	  \Repository\Component\Validation
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ValidationServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->registerValidationRule();
		$this->app->singleton(Validation::class, function($app) {
			return new Validation($app, $app[Rule::class], new Hash);
		});
	}
	
	public function registerValidationRule()
	{
		$this->app->singleton(Rule::class, function($app) {
			$rule = Rule::make();
			Request::rules($rule);
			Request::alerts($rule);
			
			return $rule;
		});
	}
}
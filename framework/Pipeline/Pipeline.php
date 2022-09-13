<?php
namespace Repository\Component\Pipeline;

use Closure;
use Repository\Component\Pipeline\Exception\PipelineException;
use Repository\Component\Contracts\Pipeline\PipelineInterface as IPipeline;

/**
 * Pipeline.
 * 
 * @package	  \Repository\Component\Pipeline
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Pipeline implements IPipeline
{
	/**
	 * The input to send through the pipeline
	 * @var mixed $input
	 */
	private $input = null;
	
	/**
	 * The list of stages to send input through
	 * @var array $stages
	 */
	private $stages = [];

	/**
	 * The method to call if the stages are not closures
	 * @var string $methodToCall
	 */
	private $methodToCall = null;
	
	/**
	 * The callback to execute at the end
	 * @var callable $callback
	 */
	private $callback = null;
	
	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Pipeline\PipelineInterface::send()
	 */
	public function send($input): IPipeline
	{
		$this->input = $input;
		
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Pipeline\PipelineInterface::then()
	 */
	public function then(callable $callback) : IPipeline
	{
		$this->callback = $callback;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Pipeline\PipelineInterface::through()
	 */
	public function through(array $stages, string $methodToCall = null) : IPipeline
	{
		$this->stages = $stages;
		$this->methodToCall = $methodToCall;

		return $this;
	}

	/**
	 * Creates a callback for an individual stage
	 *
	 * @return Closure The callback
	 * 
	 * @throws PipelineException Thrown if there was a problem creating a stage
	 */
	private function createStageCallback() : Closure
	{
		return function ($stages, $stage) {
			return function ($input) use ($stages, $stage) {
				if ($stage instanceof Closure) {
					return $stage($input, $stages);
				} else {
					if ($this->methodToCall === null) {
						throw new PipelineException('Method must not be null');
					}

					if (!method_exists($stage, $this->methodToCall)) {
						throw new PipelineException(get_class($stage) . "::{$this->methodToCall} does not exist");
					}

					return $stage->{$this->methodToCall}($input, $stages);
				}
			};
		};
	}

	/**
	 * {@inheritdoc}
	 * See \Repository\Component\Contracts\Pipeline\PipelineInterface::execute()
	 */
	public function execute()
	{
		return call_user_func(
			array_reduce(
				$this->stages, $this->createStageCallback(),
				function ($input) {
					if ($this->callback === null) {
						return $input;
					}
					return ($this->callback)($input);
				}), $this->input
		);
	}
}
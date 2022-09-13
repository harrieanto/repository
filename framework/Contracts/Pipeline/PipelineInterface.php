<?php
namespace Repository\Component\Contracts\Pipeline;

/**
 * Pipeline Interface.
 * 
 * @package	 \Repository\Component\Contracts\Pipeline
 * @author Hariyanto - harrieanto31@yahoo.com
 * @version 1.0
 * @link  https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
interface PipelineInterface
{
    /**
     * Executes the pipeline
     *
     * @return mixed The output of the pipeline
     * 
     * @throws \Repository\Component\Pipeline\Exception\PipelineException
     * Thrown if there was a problem sending the input down the pipeline
     */
    public function execute();

    /**
     * Sets the input to send through the pipeline
     *
     * @param mixed $input The input to send
     * 
     * @return self For method chaining
     */
    public function send($input) : self;

    /**
     * Sets the callback to call at the end of the pipeline
     *
     * @param callable $callback The callback to run after the pipeline
     *      It must accept the result of the pipeline as a parameter
     * 
     * @return self For method chaining
     */
    public function then(callable $callback) : self;

    /**
     * Sets the list of stages in the pipeline
     *
     * @param Closure[]|array $stages The list of stages in the pipeline
     * @param string|null $methodToCall Sets the method to call if the stages are a list of objects or class names
     * 
     * @return self For method chaining
     */
    public function through(array $stages, string $methodToCall = null) : self;
}


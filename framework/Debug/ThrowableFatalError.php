<?php
namespace Repository\Component\Debug;

use ErrorException;
use ParseError;
use Throwable;
use TypeError;

/**
 * Wrap Throwable Fatal Error.
 *
 * @package	  \Repository\Component\Debug
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class ThrowableFatalError extends ErrorException
{
    public function __construct(Throwable $error)
    {
        if ($error instanceof TypeError) {
            $message = "Type error: {$error->getMessage()}";
            $severity = E_RECOVERABLE_ERROR;
        } elseif ($error instanceof ParseError) {
            $message = "Parse error: {$error->getMessage()}";
            $severity = E_PARSE;
        } else {
            $message = "Fatal error: {$error->getMessage()}";
            $severity = E_ERROR;
        }

        parent::__construct($message, $error->getCode(), $severity, $error->getFile(), $error->getLine());
    }
}

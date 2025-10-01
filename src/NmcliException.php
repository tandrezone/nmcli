<?php

namespace Tandrezone\NmcliPhp;

/**
 * Custom exception for nmcli command failures
 */
class NmcliException extends \Exception
{
    /**
     * Constructor
     * 
     * @param string $message Exception message
     * @param int $code Exception code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
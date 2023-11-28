<?php

namespace RRZU\Validation;

class ValidationException extends \Exception
{
    public $status = 422;

    /**
     * Constructor.
     * @param string|null $message error message
     * @param int $code error code
     * @param \Throwable|null $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, $previous = null)
    {
        $status = $code != 0 ? $code : $this->status;
        parent::__construct($message, $status, $previous);
    }

}

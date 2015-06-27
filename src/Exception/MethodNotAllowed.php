<?php namespace Phprest\Exception;

use Symfony\Component\HttpFoundation\Response;

class MethodNotAllowed extends Exception
{
    /**
     * @param integer $code
     * @param array $details
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct(
        $code                   = 0,
        array $details          = [],
        $message                = 'Method Not Allowed',
        \Exception $previous    = null
    ) {
        parent::__construct($message, $code, Response::HTTP_METHOD_NOT_ALLOWED, $details, $previous);
    }
}

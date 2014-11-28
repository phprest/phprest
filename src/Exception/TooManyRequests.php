<?php namespace Phrest\Exception;

use Symfony\Component\HttpFoundation\Response;

class TooManyRequests extends Exception
{
    /**
     * @param integer $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct($code = 0,
                                array $errors = [],
                                $message = 'Too Many Requests',
                                \Exception $previous = null)
    {
        parent::__construct($message, $code, Response::HTTP_TOO_MANY_REQUESTS, $errors, $previous);
    }
}

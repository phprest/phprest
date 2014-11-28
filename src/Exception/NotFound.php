<?php namespace Phrest\Exception;

use Symfony\Component\HttpFoundation\Response;

class NotFound extends Exception
{
    /**
     * @param integer $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct($code = 0,
                                array $errors = [],
                                $message = 'Not Found',
                                \Exception $previous = null)
    {
        parent::__construct($message, $code, Response::HTTP_NOT_FOUND, $errors, $previous);
    }
}

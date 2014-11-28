<?php namespace Phrest\Exception;

use Symfony\Component\HttpFoundation\Response;

class InternalServerError extends Exception
{
    /**
     * @param integer $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct($code = 0,
                                array $errors = [],
                                $message = 'Internal Server Error',
                                \Exception $previous = null)
    {
        parent::__construct($message, $code, Response::HTTP_INTERNAL_SERVER_ERROR, $errors, $previous);
    }
}

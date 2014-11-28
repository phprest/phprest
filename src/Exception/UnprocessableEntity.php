<?php namespace Phrest\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnprocessableEntity extends Exception
{
    /**
     * @param integer $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct($code = 0,
                                array $errors = [],
                                $message = 'Unprocessable Entity',
                                \Exception $previous = null)
    {
        parent::__construct($message, $code, Response::HTTP_UNPROCESSABLE_ENTITY, $errors, $previous);
    }
}

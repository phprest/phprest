<?php namespace Phprest\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnprocessableEntity extends Exception
{
    /**
     * @param integer $code
     * @param array $details
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct($code = 0,
                                array $details = [],
                                $message = 'Unprocessable Entity',
                                \Exception $previous = null)
    {
        parent::__construct($message, $code, Response::HTTP_UNPROCESSABLE_ENTITY, $details, $previous);
    }
}

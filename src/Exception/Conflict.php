<?php namespace Phprest\Exception;

use Symfony\Component\HttpFoundation\Response;

class Conflict extends Exception
{
    /**
     * @param integer $code
     * @param array $details
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct($code = 0,
                                array $details = [],
                                $message = 'Conflict',
                                \Exception $previous = null)
    {
        parent::__construct($message, $code, Response::HTTP_CONFLICT, $details, $previous);
    }
}

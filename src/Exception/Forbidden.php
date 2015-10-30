<?php

namespace Phprest\Exception;

use Symfony\Component\HttpFoundation\Response;

class Forbidden extends Exception
{
    /**
     * @param int $code
     * @param array $details
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct(
        $code = 0,
        array $details = [],
        $message = 'Forbidden',
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, Response::HTTP_FORBIDDEN, $details, $previous);
    }
}

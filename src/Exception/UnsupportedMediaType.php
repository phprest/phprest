<?php namespace Phprest\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnsupportedMediaType extends Exception
{
    /**
     * @param integer $code
     * @param array $details
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct(
        $code = 0,
        array $details = [],
        $message = 'Unsupported Media Type',
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $details, $previous);
    }
}

<?php namespace Phrest\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnsupportedMediaType extends Exception
{
    /**
     * @param integer $code
     * @param array $errors
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct($code = 0,
                                array $errors = [],
                                $message = 'Unsupported Media Type',
                                \Exception $previous = null)
    {
        parent::__construct($message, $code, Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $errors, $previous);
    }
}

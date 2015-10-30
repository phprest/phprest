<?php

namespace Phprest\Exception;

class Exception extends \Exception
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $details;

    /**
     * @param string $message
     * @param int $code
     * @param int $statusCode
     * @param array $details
     * @param \Exception $previous
     */
    public function __construct(
        $message = '',
        $code = 0,
        $statusCode = 500,
        array $details = [],
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->statusCode   = $statusCode;
        $this->details      = $details;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }
}

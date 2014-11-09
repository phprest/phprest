<?php namespace Phrest\Exception;

class Exception extends \Exception
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @param string $message
     * @param int $code
     * @param int $statusCode
     * @param \Exception $previous
     */
    public function __construct($message = '', $code = 0, $statusCode = 500, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}

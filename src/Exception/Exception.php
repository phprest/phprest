<?php namespace Phrest\Exception;

class Exception extends \Exception
{
    /**
     * @var integer
     */
    private $statusCode;

    /**
     * @param string $message
     * @param integer $code
     * @param integer $statusCode
     * @param \Exception $previous
     */
    public function __construct($message = '', $code = 0, $statusCode = 500, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
    }

    /**
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}

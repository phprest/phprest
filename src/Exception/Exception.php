<?php namespace Phrest\Exception;

class Exception extends \Exception
{
    /**
     * @var integer
     */
    private $statusCode;

    /**
     * @var array
     */
    private $errors;

    /**
     * @param string $message
     * @param integer $code
     * @param integer $statusCode
     * @param array $errors
     * @param \Exception $previous
     */
    public function __construct($message = '',
                                $code = 0,
                                $statusCode = 500,
                                array $errors = [],
                                \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    /**
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

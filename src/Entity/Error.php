<?php namespace Phrest\Entity;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Phrest\Exception\Exception;

/**
 * @Serializer\XmlRoot("result")
 */
class Error
{
    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    private $code;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    private $message;

    /**
     * For detailed error message
     *
     * @var array
     * @Serializer\Type("array")
     */
    private $errors = [];

    /**
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->code = $exception->getCode();
        $this->message = $exception->getMessage();

        if ($exception instanceof Exception) {
            $this->errors = $exception->getErrors();
        }
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

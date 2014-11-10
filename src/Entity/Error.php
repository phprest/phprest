<?php namespace Phrest\Entity;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

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
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->code = $exception->getCode();
        $this->message = $exception->getMessage();
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
}

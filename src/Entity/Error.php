<?php

namespace Phprest\Entity;

use JMS\Serializer\Annotation as Serializer;
use Phprest\Exception\Exception;

/**
 * @Serializer\XmlRoot("result")
 */
class Error
{
    /**
     * @var int
     * @Serializer\Type("integer")
     */
    protected $code;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $message;

    /**
     * @var array
     * @Serializer\Type("array")
     */
    protected $details = [];

    /**
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->code     = $exception->getCode();
        $this->message  = $exception->getMessage();

        if ($exception instanceof Exception) {
            $this->details = $exception->getDetails();
        }
    }

    /**
     * @return int
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
    public function getDetails()
    {
        return $this->details;
    }
}

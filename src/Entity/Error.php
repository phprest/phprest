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
     * @Serializer\Type("integer")
     */
    protected int $code;

    /**
     * @Serializer\Type("string")
     */
    protected string $message;

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
        $this->code     = (int) $exception->getCode();
        $this->message  = $exception->getMessage();

        if ($exception instanceof Exception) {
            $this->details = $exception->getDetails();
        }
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}

<?php

namespace Phprest\Entity;

use Exception;
use JMS\Serializer\Annotation as Serializer;

class DebugError extends Error
{
    /**
     * @Serializer\Type("string")
     */
    protected string $fileName;

    /**
     * @Serializer\Type("integer")
     */
    protected int $line;

    /**
     * @Serializer\Type("string")
     */
    protected string $trace;

    public function __construct(Exception $exception)
    {
        parent::__construct($exception);

        $this->fileName = $exception->getFile();
        $this->line     = $exception->getLine();
        $this->trace    = $exception->getTraceAsString();
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getTrace(): string
    {
        return $this->trace;
    }
}

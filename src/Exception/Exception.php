<?php

namespace Phprest\Exception;

class Exception extends \Exception
{
    private int $statusCode;
    private array $details;

    public function __construct(
        string $message = '',
        int $code = 0,
        int $statusCode = 500,
        array $details = [],
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->statusCode   = $statusCode;
        $this->details      = $details;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}

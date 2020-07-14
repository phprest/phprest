<?php

namespace Phprest\ErrorHandler\Handler;

use ErrorException;
use Exception;
use League\BooBoo\Handler\HandlerInterface;
use Phprest\Exception\Exception as PhprestException;
use Psr\Log\LoggerInterface;

class Log implements HandlerInterface
{
    protected ?LoggerInterface $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        if (null !== $logger) {
            $this->setLogger($logger);
        }
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param Exception $exception
     */
    public function handle($exception): void
    {
        if ($exception instanceof ErrorException) {
            $this->handleErrorException($exception);

            return;
        }

        if ($this->logger) {
            $this->logger->critical($this->buildLogMessage($exception));
        }
    }

    protected function handleErrorException(ErrorException $exception): bool
    {
        switch ($exception->getSeverity()) {
            case E_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_PARSE:
                $this->logger->error($this->buildLogMessage($exception));
                break;

            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                $this->logger->warning($this->buildLogMessage($exception));
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
                $this->logger->notice($this->buildLogMessage($exception));
                break;

            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $this->logger->info($this->buildLogMessage($exception));
                break;
        }

        return true;
    }

    protected function buildLogMessage(Exception $exception): string
    {
        $message = $exception->getMessage() . "({$exception->getCode()})";

        if ($exception instanceof PhprestException && $exception->getDetails()) {
            $message .= ' Details :: ' . json_encode($exception->getDetails());
        }

        $message .= ' Stack trace :: ' . $exception->getTraceAsString();

        return $message;
    }
}

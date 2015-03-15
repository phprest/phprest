<?php namespace Phprest\ErrorHandler\Handler;

use Phprest\Service;
use Phprest\Entity;
use Phprest\Exception\Exception as PhprestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\BooBoo\Handler\HandlerInterface;
use Psr\Log\LoggerInterface;

class Log implements HandlerInterface
{
    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        if (! is_null($logger)) {
            $this->setLogger($logger);
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Exception $exception
     */
    public function handle(\Exception $exception)
    {
        if ($exception instanceof \ErrorException) {
            $this->handleErrorException($exception);

            return;
        }

        $this->logger->critical($this->buildLogMessage($exception));
    }

    /**
     * @param \ErrorException $exception
     *
     * @return bool
     */
    protected function handleErrorException(\ErrorException $exception)
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

    /**
     * @param \Exception $exception
     *
     * @return string
     */
    protected function buildLogMessage(\Exception $exception)
    {
        $message = $exception->getMessage() . "({$exception->getCode()})";

        if ($exception instanceof PhprestException && $exception->getDetails()) {
            $message .= ' Details :: ' . json_encode($exception->getDetails());
        }

        $message .= ' Stack trace :: ' . $exception->getTraceAsString();

        return $message;
    }
}

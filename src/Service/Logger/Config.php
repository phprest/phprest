<?php

namespace Phprest\Service\Logger;

use Monolog\Handler\HandlerInterface;
use Phprest\Service\Configurable;

class Config implements Configurable
{

    public string $name;

    /**
     * @var HandlerInterface[]
     */
    public array $handlers;

    /**
     * @param HandlerInterface[] $handlers
     */
    public function __construct(string $name, array $handlers = [])
    {
        $this->name     = $name;
        $this->handlers = $handlers;
    }

    public static function getServiceName(): string
    {
        return 'logger';
    }
}

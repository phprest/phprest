<?php

namespace Phprest\Service\Logger;

use Monolog\Handler\HandlerInterface;
use Phprest\Service\Configurable;

class Config implements Configurable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var HandlerInterface[]
     */
    public $handlers;

    /**
     * @param string $name
     * @param HandlerInterface[] $handlers
     */
    public function __construct($name, array $handlers = [])
    {
        $this->name     = $name;
        $this->handlers = $handlers;
    }

    public static function getServiceName(): string
    {
        return 'logger';
    }
}

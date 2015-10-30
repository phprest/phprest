<?php

namespace Phprest\Service\Logger;

use Phprest\Service\Configurable;

class Config implements Configurable
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var \Monolog\Handler\HandlerInterface[]
     */
    public $handlers;

    /**
     * @param string $name
     * @param \Monolog\Handler\HandlerInterface[] $handlers
     */
    public function __construct($name, array $handlers = [])
    {
        $this->name     = $name;
        $this->handlers = $handlers;
    }

    /**
     * @return string
     */
    public static function getServiceName()
    {
        return 'logger';
    }
}

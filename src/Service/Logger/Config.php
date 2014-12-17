<?php namespace Phprest\Service\Logger;

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
     * @var string
     */
    public $prodErrorMessage;

    /**
     * @param string $name
     * @param \Monolog\Handler\HandlerInterface[] $handlers
     * @param string $prodErrorMessage
     */
    public function __construct($name, array $handlers = [], $prodErrorMessage = 'Server Error')
    {
        $this->name = $name;
        $this->handlers = $handlers;
        $this->prodErrorMessage = $prodErrorMessage;
    }

    /**
     * @return string
     */
    static public function getServiceName()
    {
        return 'logger';
    }
}

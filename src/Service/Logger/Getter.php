<?php

namespace Phprest\Service\Logger;

use League\Container\Container;
use League\Container\ContainerInterface;
use Monolog\Logger;

trait Getter
{

    protected function serviceLogger(): Logger
    {
        return $this->getContainer()->get(Config::getServiceName());
    }

    /**
     * Returns the DI container.
     *
     * @return Container|ContainerInterface
     */
    abstract protected function getContainer();
}

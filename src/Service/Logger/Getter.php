<?php

namespace Phprest\Service\Logger;

trait Getter
{
    /**
     * @return \Monolog\Logger
     */
    protected function serviceLogger()
    {
        return $this->getContainer()->get(Config::getServiceName());
    }

    /**
     * Returns the DI container.
     *
     * @return \League\Container\ContainerInterface
     */
    abstract protected function getContainer();
}

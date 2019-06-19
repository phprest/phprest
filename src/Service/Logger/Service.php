<?php

namespace Phprest\Service\Logger;

use InvalidArgumentException;
use League\Container\ContainerInterface;
use Monolog\Logger;
use Phprest\Service\Configurable;
use Phprest\Service\Serviceable;

class Service implements Serviceable
{
    /**
     * @param ContainerInterface $container
     * @param Configurable $config
     *
     * @return void
     */
    public function register(ContainerInterface $container, Configurable $config): void
    {
        if (! $config instanceof Config) {
            throw new InvalidArgumentException('Wrong Config object');
        }

        $logger = new Logger($config->name, $config->handlers);

        $container->add($config->getServiceName(), $logger);
    }
}

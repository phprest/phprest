<?php namespace Phprest\Service\Logger;

use Phprest\Service\Serviceable;
use Phprest\Service\Configurable;
use League\Container\ContainerInterface;
use Monolog\Logger;

class Service implements Serviceable
{
    /**
     * @param ContainerInterface $container
     * @param Configurable $config
     *
     * @return void
     */
    public function register(ContainerInterface $container, Configurable $config)
    {
        if (! $config instanceof Config) {
            throw new \InvalidArgumentException('Wrong Config object');
        }

        $logger = new Logger($config->name, $config->handlers);

        $container->add($config->getServiceName(), $logger);
    }
}

<?php

namespace Phprest\Service;

use League\Container\ContainerInterface;

interface Serviceable
{
    /**
     * @param ContainerInterface $container
     * @param Configurable $config
     *
     * @return void
     */
    public function register(ContainerInterface $container, Configurable $config);
}

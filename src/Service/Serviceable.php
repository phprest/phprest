<?php

namespace Phprest\Service;

use League\Container\ContainerInterface;

interface Serviceable
{
    public function register(ContainerInterface $container, Configurable $config): void;
}

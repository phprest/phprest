<?php namespace Phprest\Service;

use League\Container\Container;

interface Serviceable
{
    /**
     * @param Container $container
     * @param Configurable $config
     *
     * @return void
     */
    public function register(Container $container, Configurable $config);
}

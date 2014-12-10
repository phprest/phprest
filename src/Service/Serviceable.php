<?php namespace Phprest\Service;

use Orno\Di\Container;

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

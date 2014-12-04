<?php namespace Phrest\Service\Hateoas;

use Phrest\Service\Contract\Serviceable;
use Phrest\Service\Contract\Configurable;
use Orno\Di\Container;
use Hateoas\HateoasBuilder;

class Service implements Serviceable
{
    /**
     * @param Container $container
     * @param Configurable $config
     *
     * @return void
     */
    public function register(Container $container, Configurable $config)
    {
        if ( ! $config instanceof Config) {
            throw new \InvalidArgumentException('Wrong Config object');
        }

        $hateoas = HateoasBuilder::create();

        $hateoas->setDebug($config->debug);
        $hateoas->setCacheDir($config->cacheDir);
        $hateoas->addMetadataDir($config->metadataDir);

        $container->singleton($config->getServiceName(), $hateoas->build());
    }
}

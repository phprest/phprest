<?php namespace Phrest\Service\BuiltIn\Hateoas;

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
        $hateoas = HateoasBuilder::create();

        /** @var Config $config */

        $hateoas->setDebug($config->debug);
        $hateoas->setCacheDir($config->cacheDir);
        $hateoas->addMetadataDir($config->metadataDir);

        $container->singleton($config->getServiceName(), $hateoas->build());
    }
}

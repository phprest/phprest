<?php namespace Phrest\Service\Hateoas;

use Phrest\Service\Serviceable;
use Phrest\Service\Configurable;
use Orno\Di\Container;
use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\CallableUrlGenerator;

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
        $hateoas->setUrlGenerator(null, new CallableUrlGenerator($config->urlGenerator));

        $container->singleton($config->getServiceName(), $hateoas->build());
    }
}

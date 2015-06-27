<?php namespace Phprest\Service\Hateoas;

use Phprest\Service\Serviceable;
use Phprest\Service\Configurable;
use League\Container\ContainerInterface;
use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\CallableUrlGenerator;

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

        $hateoas = HateoasBuilder::create();

        $hateoas->setDebug($config->debug);
        $hateoas->setUrlGenerator(null, new CallableUrlGenerator($config->urlGenerator));

        if (! $config->debug) {
            $hateoas->setCacheDir($config->cacheDir);
            $hateoas->addMetadataDir($config->metadataDir);
        }

        $container->add($config->getServiceName(), $hateoas->build());
    }
}

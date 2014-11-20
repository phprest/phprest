<?php namespace Phrest\Service\BuiltIn\Serializer;

use Phrest\Service\Contract\Serviceable;
use Phrest\Service\Contract\Configurable;
use Orno\Di\Container;
use JMS\Serializer\SerializerBuilder;

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
        $serializer = SerializerBuilder::create();

        /** @var Config $config */

        $serializer->setDebug($config->debug);

        if ( ! is_null($config->cacheDir)) {
            $serializer->setCacheDir($config->cacheDir);
        }

        if ( ! is_null($config->metadataDir)) {
            $serializer->addMetadataDir($config->metadataDir);
        }

        $container->add($config->getServiceName(), $serializer->build());
    }
}

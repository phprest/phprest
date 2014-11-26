<?php namespace Phrest\Service\BuiltIn\Serializer;

trait Getter
{
    /**
     * @return \JMS\Serializer\Serializer
     */
    public function serviceSerializer()
    {
        return $this->getContainer()->get(Config::getServiceName());
    }

    /**
     * Returns the DI container
     *
     * @return \Orno\Di\Container
     */
    abstract public function getContainer();
}

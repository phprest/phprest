<?php namespace Phrest\Service\BuiltIn\Hateoas;

trait Getter
{
    /**
     * @return \Hateoas\Hateoas
     */
    public function serviceHateoas()
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

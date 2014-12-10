<?php namespace Phprest\Service\Hateoas;

trait Getter
{
    /**
     * @return \Hateoas\Hateoas
     */
    protected function serviceHateoas()
    {
        return $this->getContainer()->get(Config::getServiceName());
    }

    /**
     * Returns the DI container
     *
     * @return \Orno\Di\Container
     */
    abstract protected function getContainer();
}

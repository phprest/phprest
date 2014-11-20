<?php namespace Phrest\Service;

trait Util
{
    /**
     * @param string $className
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getRegisteredService($className)
    {
        if ( ! class_exists($className)) {
            throw new \Exception('Class <' . $className . '> does not exist!');
        }

        /** @var Contract\Configurable $className */
        return $this->getContainer()->get($className::getServiceName());
    }

    /**
     * Returns the DI container
     *
     * @return \Orno\Di\Container
     */
    abstract public function getContainer();
}

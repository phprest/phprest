<?php namespace Phrest\Service;

/**
 * With this trait you can reach the Application's services through methods.
 *
 * All the "getters" should start with "service".
 */
trait Getters
{
    use Util;

    /**
     * @return \JMS\Serializer\Serializer
     */
    public function serviceSerializer()
    {
        return $this->getRegisteredService('\Phrest\Service\BuiltIn\Serializer\Config');
    }

    /**
     * @return \Hateoas\Hateoas
     */
    public function serviceHateoas()
    {
        return $this->getRegisteredService('\Phrest\Service\BuiltIn\Hateoas\Config');
    }
}

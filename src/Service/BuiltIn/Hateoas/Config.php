<?php namespace Phrest\Service\BuiltIn\Hateoas;

use Phrest\Service\BuiltIn;

class Config extends BuiltIn\Serializer\Config
{
    /**
     * @return string
     */
    static public function getServiceName()
    {
        return 'Hateoas';
    }
}

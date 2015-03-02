<?php namespace Phprest\Stub\Service;

use Phprest\Service\Configurable;

class SampleConfig implements Configurable
{
    static public function getServiceName()
    {
        return 'sample';
    }
}

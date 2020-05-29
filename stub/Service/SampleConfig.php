<?php

namespace Phprest\Stub\Service;

use Phprest\Service\Configurable;

class SampleConfig implements Configurable
{
    public static function getServiceName(): string
    {
        return 'sample';
    }
}

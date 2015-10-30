<?php

namespace Phprest\Stub\Entity;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("result")
 */
class Sample
{
    /**
     * @var int
     * @Serializer\Type("integer")
     */
    public $a;

    /**
     * @var int
     * @Serializer\Type("integer")
     */
    public $b;
}

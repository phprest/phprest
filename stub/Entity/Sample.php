<?php

namespace Phprest\Stub\Entity;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("result")
 */
class Sample
{
    /**
     * @Serializer\Type("integer")
     */
    public int $a;

    /**
     * @Serializer\Type("integer")
     */
    public int $b;
}

<?php namespace Phprest\Stub\Entity;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\XmlRoot("result")
 */
class Sample
{
    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    public $a;

    /**
     * @var integer
     * @Serializer\Type("integer")
     */
    public $b;
}

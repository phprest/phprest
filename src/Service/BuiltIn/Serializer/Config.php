<?php namespace Phrest\Service\BuiltIn\Serializer;

use Phrest\Service\Contract\Configurable;

class Config implements Configurable
{
    /**
     * @var boolean
     */
    public $debug = false;

    /**
     * @var string
     */
    public $cacheDir = '/tmp/serializer';

    /**
     * @var string
     */
    public $metadataDir = '/tmp/serializer';

    /**
     * @param boolean $debug
     * @param string $cacheDir
     * @param string $metadataDir
     */
    public function __construct($debug = false, $cacheDir = '/tmp/serializer', $metadataDir = '/tmp/serializer')
    {
        $this->debug = $debug;
        $this->cacheDir = $cacheDir;
        $this->metadataDir = $metadataDir;
    }

    /**
     * @return string
     */
    static public function getServiceName()
    {
        return 'serializer';
    }
}

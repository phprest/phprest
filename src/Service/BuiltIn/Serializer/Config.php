<?php namespace Phrest\Service\BuiltIn\Serializer;

use Phrest\Service\Contract\Configurable;

class Config implements Configurable
{
    /**
     * @var boolean
     */
    public $debug = false;

    /**
     * @var string|null
     */
    public $cacheDir = null;

    /**
     * @var string|null
     */
    public $metadataDir = null;

    /**
     * @param boolean $debug
     * @param string|null $cacheDir
     * @param string|null $metadataDir
     */
    public function __construct($debug, $cacheDir = null, $metadataDir = null)
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
        return 'Serializer';
    }
}

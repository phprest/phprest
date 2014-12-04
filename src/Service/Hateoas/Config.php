<?php namespace Phrest\Service\Hateoas;

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
    public $cacheDir = '/tmp/hateoas';

    /**
     * @var string
     */
    public $metadataDir = '/tmp/hateoas';

    /**
     * @param boolean $debug
     * @param string $cacheDir
     * @param string $metadataDir
     */
    public function __construct($debug = false, $cacheDir = '/tmp/hateoas', $metadataDir = '/tmp/hateoas')
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
        return 'hateoas';
    }
}

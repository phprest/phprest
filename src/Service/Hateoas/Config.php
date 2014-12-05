<?php namespace Phrest\Service\Hateoas;

use Phrest\Service\Configurable;
use Symfony\Component\HttpFoundation\Request;

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
     * @var callable
     */
    public $urlGenerator;

    /**
     * @param boolean $debug
     * @param string $cacheDir
     * @param string $metadataDir
     * @param callable $urlGenerator
     */
    public function __construct($debug = false,
                                $cacheDir = '/tmp/hateoas',
                                $metadataDir = '/tmp/hateoas',
                                $urlGenerator = null)
    {
        $this->debug = $debug;
        $this->cacheDir = $cacheDir;
        $this->metadataDir = $metadataDir;
        $this->urlGenerator = $urlGenerator;

        if (is_null($urlGenerator)) {
            $this->urlGenerator = function ($route, array $parameters, $absolute) {
                if ($absolute) {
                    return Request::createFromGlobals()->getSchemeAndHttpHost() . $route . '/' . implode('/', $parameters);
                }

                return $route . '/' . implode('/', $parameters);
            };
        }
    }

    /**
     * @return string
     */
    static public function getServiceName()
    {
        return 'hateoas';
    }
}

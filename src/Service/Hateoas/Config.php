<?php namespace Phprest\Service\Hateoas;

use Phprest\Service\Configurable;
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
    public $cacheDir;

    /**
     * @var string
     */
    public $metadataDir;

    /**
     * @var callable
     */
    public $urlGenerator;

    /**
     * @param boolean $debug
     * @param string|null $cacheDir
     * @param string|null $metadataDir
     * @param callable|null $urlGenerator
     */
    public function __construct(
        $debug          = false,
        $cacheDir       = null,
        $metadataDir    = null,
        $urlGenerator   = null
    ) {
        $this->debug        = $debug;
        $this->cacheDir     = $cacheDir;
        $this->metadataDir  = $metadataDir;
        $this->urlGenerator = $urlGenerator;

        if (is_null($cacheDir)) {
            $this->cacheDir = sys_get_temp_dir() . '/hateoas';
        }

        if (is_null($metadataDir)) {
            $this->metadataDir = sys_get_temp_dir() . '/hateoas';
        }

        if (is_null($urlGenerator)) {
            $this->urlGenerator = function ($route, array $parameters, $absolute) {

                $queryParams    = '';
                $resourceParams = [];

                foreach ($parameters as $paramName => $paramValue) {
                    if (strpos(strtolower($paramName), 'id') !== false) {
                        $resourceParams[$paramName] = $paramValue;
                        continue;
                    }

                    $queryParams .= $paramName . '=' . $paramValue . '&';
                }

                if ($queryParams !== '') {
                    $queryParams = '?' . substr($queryParams, 0, -1);
                }

                $resourceParams = implode('/', $resourceParams);
                if (! empty($resourceParams)) {
                    $resourceParams = '/' . $resourceParams;
                }

                if ($absolute) {
                    return Request::createFromGlobals()->getSchemeAndHttpHost() .
                    $route .
                    $resourceParams .
                    $queryParams;
                }

                return $route . $resourceParams . $queryParams;
            };
        }
    }

    /**
     * @return string
     */
    public static function getServiceName()
    {
        return 'hateoas';
    }
}

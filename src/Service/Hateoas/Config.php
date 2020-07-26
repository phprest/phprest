<?php

namespace Phprest\Service\Hateoas;

use Phprest\Service\Configurable;
use Symfony\Component\HttpFoundation\Request;

class Config implements Configurable
{
    public bool $debug = false;
    public ?string $cacheDir;
    public ?string $metadataDir;

    protected function generateUrl(string $route, array $parameters, bool $absolute): string
    {
        $queryParams    = '';
        $resourceParams = [];

        foreach ($parameters as $paramName => $paramValue) {
            if (stripos($paramName, 'id') !== false) {
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
    }

    /**
     * @var callable
     */
    public $urlGenerator;

    public function __construct(
        bool $debug = false,
        ?string $cacheDir = null,
        ?string $metadataDir = null,
        ?callable $urlGenerator = null
    ) {
        $this->debug        = $debug;
        $this->cacheDir     = $cacheDir;
        $this->metadataDir  = $metadataDir;
        $this->urlGenerator = $urlGenerator;

        if (null === $cacheDir) {
            $this->cacheDir = sys_get_temp_dir() . '/hateoas';
        }

        if (null === $metadataDir) {
            $this->metadataDir = sys_get_temp_dir() . '/hateoas';
        }

        if (null === $urlGenerator) {
            $this->urlGenerator = function ($route, array $parameters, $absolute) {
                return $this->generateUrl($route, $parameters, $absolute);
            };
        }
    }

    public static function getServiceName(): string
    {
        return 'hateoas';
    }
}

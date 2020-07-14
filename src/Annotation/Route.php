<?php

namespace Phprest\Annotation;

use InvalidArgumentException;
use Phprest\Application;

/**
 * @Annotation
 *
 * @Target("METHOD")
 */
class Route
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var string
     */
    public $path;

    /**
     * @var null|string
     */
    public $version;

    /**
     * @param mixed $options
     *
     * @throws InvalidArgumentException
     */
    public function __construct($options)
    {
        $this->validate($options);

        $version = null;

        if (isset($options['since'], $options['until'])) {
            $version = $this->getSinceUntilRegExp($options['since'], $options['until']);
        } elseif (isset($options['since'])) {
            $version = $this->getSinceRegExp($options['since']);
        } elseif (isset($options['until'])) {
            $version = $this->getUntilRegExp($options['until']);
        }

        $this->method   = $options['method'];
        $this->path     = $options['path'];
        $this->version  = null !== $version ? '{version:' . $version . '}' : '{version:any}';
    }

    /**
     * @param array $options
     *
     * @return void
     */
    protected function validate(array $options): void
    {
        if (! isset($options['method'])) {
            throw new InvalidArgumentException('method property is missing');
        } elseif (! in_array($options['method'], ['GET', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE', 'HEAD'])) {
            throw new InvalidArgumentException('method property is not valid');
        } elseif (! isset($options['path'])) {
            throw new InvalidArgumentException('path property is missing');
        } elseif (isset($options['since'])
            && ! preg_match('#^' . Application::API_VERSION_REG_EXP . '$#', $options['since'])) {
            throw new InvalidArgumentException('since property is not valid');
        } elseif (isset($options['until'])
            && ! preg_match('#^' . Application::API_VERSION_REG_EXP . '$#', $options['until'])) {
            throw new InvalidArgumentException('until property is not valid');
        }
    }

    /**
     * @param string $sinceVersion
     * @param string $untilVersion
     *
     * @return string
     */
    protected function getSinceUntilRegExp($sinceVersion, $untilVersion)
    {
        $sinceVersion = str_pad($sinceVersion, 3, '.0');
        $untilVersion = str_pad($untilVersion, 3, '.0');

        if (! ($sinceVersion < $untilVersion)) {
            throw new \LogicException('since must be lesser than until');
        }

        if ($sinceVersion[0] === $untilVersion[0]) {
            return sprintf(
                '(?:%d\.[%d-%d])',
                $sinceVersion[0],
                $sinceVersion[2],
                $untilVersion[2]
            );
        } elseif (abs($sinceVersion[0] - $untilVersion[0]) === 1) {
            return sprintf(
                '(?:%d\.[%d-9])|(?:%d\.[0-%d])',
                $sinceVersion[0],
                $sinceVersion[2],
                $untilVersion[0],
                $untilVersion[2]
            );
        } else {
            return sprintf(
                '(?:%d\.[%d-9])|(?:%d\.[0-%d])|(?:[%d-%d]\.\d)',
                $sinceVersion[0],
                $sinceVersion[2],
                $untilVersion[0],
                $untilVersion[2],
                9 < $sinceVersion[0] + 1 ? 9 : $sinceVersion[0] + 1,
                0 > $untilVersion[0] - 1 ? 0 : $untilVersion[0] - 1
            );
        }
    }

    /**
     * @param string $version
     *
     * @return string
     */
    protected function getSinceRegExp($version): string
    {
        $version = str_pad($version, 3, '.0');

        return sprintf(
            '(?:[%d-9]\.[%d-9])|(?:[%d-9]\.\d)',
            $version[0],
            $version[2],
            9 < $version[0] + 1 ? 9 : $version[0] + 1
        );
    }

    /**
     * @param string $version
     *
     * @return string
     */
    protected function getUntilRegExp($version): string
    {
        $version = str_pad($version, 3, '.0');

        return sprintf(
            '(?:[0-%d]\.[0-%d])|(?:[0-%d]\.\d)',
            $version[0],
            $version[2],
            0 > $version[0] - 1 ? 0 : $version[0] - 1
        );
    }
}

<?php

namespace Phprest\Util;

use League\Container\Container;
use Phprest\Application;
use Phprest\Util\DataStructure\MimeProcessResult;

trait Mime
{
    /**
     * @param string $mime
     *
     * @return MimeProcessResult
     */
    protected function processMime($mime)
    {
        $vendor             = $this->getContainer()->get(Application::CONTAINER_ID_VENDOR);
        $apiVersion         = $this->getContainer()->get(Application::CONTAINER_ID_API_VERSION);
        $apiVersionRegExp   = Application::API_VERSION_REG_EXP;
        $format             = null;

        if (preg_match(
            '#application/vnd\.' . $vendor . '-v' . $apiVersionRegExp . '\+(xml|json)#',
            $mime,
            $matches
        )) {
            list($mime, $apiVersion, $format) = $matches;
        } elseif (preg_match(
            '#application/vnd\.' . $vendor . '\+(xml|json).*?version=' . $apiVersionRegExp . '#',
            $mime,
            $matches
        )) {
            list($mime, $format, $apiVersion) = $matches;
        } elseif ('application/json' === $mime) {
            $format = 'json';
            $mime   = 'application/vnd.' . $vendor . '-v' . $apiVersion . '+json';
        } elseif ('application/xml' === $mime) {
            $format = 'xml';
            $mime   = 'application/vnd.' . $vendor . '-v' . $apiVersion . '+xml';
        }

        return new MimeProcessResult($mime, $vendor, $apiVersion, $format);
    }

    /**
     * Returns the DI container.
     *
     * @return Container
     */
    abstract protected function getContainer();
}

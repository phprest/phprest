<?php namespace Phrest\Service\BuiltIn\Serializer;

use Phrest\Negotiate\Mime;
use Symfony\Component\HttpFoundation\Request;

trait Util
{
    /**
     * @param Request $request
     *
     * @return string json or xml
     */
    protected function getContentFormat(Request $request)
    {
        $format = 'json';
        $contentType = $request->headers->get('Content-Type', Mime::JSON);
        $contentType = explode(';', $contentType)[0];

        if ($contentType === Mime::JSON) {
            $format = 'json';
        } elseif ($contentType === Mime::XML) {
            $format = 'xml';
        }

        return $format;
    }

    /**
     * @param string $type
     * @param Request $request
     *
     * @return mixed
     */
    protected function deserialize($type, Request $request)
    {
        return $this->serviceSerializer()->deserialize(
            $request->getContent(),
            $type,
            $this->getContentFormat($request)
        );
    }

    /**
     * @param string $type
     * @param Request $request
     *
     * @return mixed
     */
    protected function deserializeJson($type, Request $request)
    {
        return $this->serviceSerializer()->deserialize(
            $request->getContent(),
            $type,
            'json'
        );
    }

    /**
     * @param string $type
     * @param Request $request
     *
     * @return mixed
     */
    protected function deserializeXml($type, Request $request)
    {
        return $this->serviceSerializer()->deserialize(
            $request->getContent(),
            $type,
            'xml'
        );
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    abstract public function serviceSerializer();
}

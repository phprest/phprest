<?php namespace Phrest\Service\Hateoas;

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
        $contentType = $request->headers->get('Content-Type', 'application/json');
        $contentType = explode(';', $contentType)[0];

        if ($contentType === 'application/json') {
            $format = 'json';
        } elseif ($contentType === 'application/xml') {
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
        return $this->serviceHateoas()->getSerializer()->deserialize(
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
        return $this->serviceHateoas()->getSerializer()->deserialize(
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
        return $this->serviceHateoas()->getSerializer()->deserialize(
            $request->getContent(),
            $type,
            'xml'
        );
    }

    /**
     * @return \Hateoas\Hateoas
     */
    abstract protected function serviceHateoas();
}

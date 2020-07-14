<?php

namespace Phprest\Service\Hateoas;

use Hateoas\Hateoas;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Negotiation\FormatNegotiator;
use Phprest\Exception;
use Phprest\Util\Mime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait Util
{
    use Mime;

    /**
     * @param mixed $value
     * @param Request $request
     * @param Response $response
     *
     * @throws Exception\NotAcceptable
     *
     * @return Response
     */
    protected function serialize($value, Request $request, Response $response)
    {
        $mimeProcResult = $this->processMime(
            (new FormatNegotiator())->getBest($request->headers->get('Accept', '*/*'))->getValue()
        );

        if ($mimeProcResult->mime === '*/*') {
            $mimeProcResult->mime = 'application/vnd.' . $mimeProcResult->vendor .
                '+json; version=' . $mimeProcResult->apiVersion;
            $mimeProcResult->format = 'json';
        }

        if (in_array($mimeProcResult->format, ['json', 'xml'])) {
            $response->setContent(
                $this->serviceHateoas()->serialize(
                    $value,
                    $mimeProcResult->format,
                    SerializationContext::create()->setVersion($mimeProcResult->apiVersion)
                )
            );

            $response->headers->set('Content-Type', $mimeProcResult->mime);

            return $response;
        }

        throw new Exception\NotAcceptable(0, [$mimeProcResult->mime . ' is not supported']);
    }

    /**
     * @param string $type
     * @param Request $request
     *
     * @throws Exception\UnsupportedMediaType
     *
     * @return mixed
     */
    protected function deserialize($type, Request $request)
    {
        $mimeProcResult = $this->processMime($request->headers->get('Content-Type'));

        if (is_null($mimeProcResult->format)) {
            throw new Exception\UnsupportedMediaType();
        }

        return $this->serviceHateoas()->getSerializer()->deserialize(
            $request->getContent(),
            $type,
            $mimeProcResult->format,
            DeserializationContext::create()->setVersion($mimeProcResult->apiVersion)
        );
    }

    /**
     * @return Hateoas
     */
    abstract protected function serviceHateoas();
}

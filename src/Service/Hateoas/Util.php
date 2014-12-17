<?php namespace Phprest\Service\Hateoas;

use Phprest\Application;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\DeserializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Negotiation\FormatNegotiator;
use Phprest\Service\Hateoas\DataStructure\MimeProcessResult;
use Phprest\Exception;

trait Util
{
    /**
     * @param mixed $value
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws Exception\NotAcceptable
     */
    protected function serialize($value, Request $request, Response $response)
    {
        $mimeProcResult = $this->processMime(
            (new FormatNegotiator())->getBest($request->headers->get('Accept', '*/*'))->getValue()
        );

        $this->apiVersionHandler($mimeProcResult);

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

        throw new Exception\NotAcceptable(PHP_INT_MAX - 2, [$mimeProcResult->mime . ' is not supported']);
    }

    /**
     * @param string $type
     * @param Request $request
     *
     * @return mixed
     *
     * @throws Exception\UnsupportedMediaType
     */
    protected function deserialize($type, Request $request)
    {
        $mimeProcResult = $this->processMime($request->headers->get('Content-Type'));

        if (is_null($mimeProcResult->format)) {
            throw new Exception\UnsupportedMediaType(PHP_INT_MAX - 3);
        }

        $this->apiVersionHandler($mimeProcResult);

        return $this->serviceHateoas()->getSerializer()->deserialize(
            $request->getContent(),
            $type,
            $mimeProcResult->format,
            DeserializationContext::create()->setVersion($mimeProcResult->apiVersion)
        );
    }

    /**
     * @param string $mime
     *
     * @return MimeProcessResult
     */
    protected function processMime($mime)
    {
        $vendor = $this->getContainer()->get(Application::CNTRID_VENDOR);
        $apiVersion = $this->getContainer()->get(Application::CNTRID_API_VERSION);
        $format = null;

        if (preg_match('#application/vnd\.' . $vendor . '-v([0-9\.]+)\+(xml|json)#', $mime, $matches)) {
            list($mime, $apiVersion, $format) = $matches;
        } elseif (preg_match('#application/vnd\.' . $vendor . '\+(xml|json).*?version=([0-9\.]+)#', $mime, $matches)) {
            list($mime, $format, $apiVersion) = $matches;
        } elseif ('application/json' === $mime) {
            $format = 'json';
            $mime = 'application/vnd.' . $vendor . '-v' . $apiVersion . '+json';
        } elseif ('application/xml' === $mime) {
            $format = 'xml';
            $mime = 'application/vnd.' . $vendor . '-v' . $apiVersion . '+xml';
        }

        return new MimeProcessResult($mime, $vendor, $apiVersion, $format);
    }

    /**
     * @param MimeProcessResult $mimeProcResult
     *
     * @return void
     */
    protected function apiVersionHandler(MimeProcessResult $mimeProcResult)
    {
        if ( ! is_null($mimeProcResult->format) and
            is_callable($this->getContainer()->get(Application::CNTRID_API_VERSION_HANDLER))) {

            call_user_func(
                $this->getContainer()->get(Application::CNTRID_API_VERSION_HANDLER),
                $mimeProcResult->apiVersion
            );

        }
    }

    /**
     * Returns the DI container
     *
     * @return \Orno\Di\Container
     */
    abstract protected function getContainer();

    /**
     * @return \Hateoas\Hateoas
     */
    abstract protected function serviceHateoas();
}

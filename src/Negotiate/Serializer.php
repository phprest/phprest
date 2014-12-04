<?php namespace Phrest\Negotiate;

use Phrest\Application;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Negotiation\FormatNegotiator;
use Phrest\Exception;

trait Serializer
{
    /**
     * @param mixed $value
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws Exception\PreconditionFailed
     */
    protected function serialize($value, Request $request, Response $response)
    {
        $request = $this->getNegotiatedRequest($request);

        $vendor = $request->attributes->get('_vendor');
        $apiVersion = $request->attributes->get('_api_version');
        $format = $request->attributes->get('_format');
        $mime = $request->attributes->get('_mime');

        if ($mime === '*/*') {
            $mime = 'application/vnd.' . $vendor . '+json; version=' . $apiVersion;
            $format = 'json';
        }

        if (in_array($format, ['json', 'xml'])) {
            $response->setContent(
                $this->serviceHateoas()->serialize(
                    $value,
                    $format,
                    SerializationContext::create()->setVersion($apiVersion)
                )
            );

            $response->headers->set('Content-Type', $mime);

            return $response;
        }

        throw new Exception\PreconditionFailed(PHP_INT_MAX - 2, [$mime . ' does not supported']);
    }

    /**
     * @param Request $request
     *
     * @return Request
     */
    protected function getNegotiatedRequest(Request $request)
    {
        $negotiator = new FormatNegotiator();

        $mime = $negotiator->getBest($request->headers->get('Accept'))->getValue();
        $vendor = $this->getContainer()->get(Application::CONFIG_VENDOR);
        $apiVersion = $this->getContainer()->get(Application::CONFIG_API_VERSION);
        $format = null;

        if (preg_match('#application/vnd\.' . $vendor . '-v([0-9\.]+)\+(xml|json)#', $mime, $matches)) {

            list($mime, $apiVersion, $format) = $matches;

        } elseif (preg_match('#application/vnd\.' . $vendor . '\+(xml|json).*?version=([0-9\.]+)#', $mime, $matches)) {

            list($mime, $format, $apiVersion) = $matches;

        }

        $request->attributes->set('_vendor', $vendor);
        $request->attributes->set('_api_version', $apiVersion);
        $request->attributes->set('_format', $format);
        $request->attributes->set('_mime', $mime);

        return $request;
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

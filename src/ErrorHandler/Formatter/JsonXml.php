<?php namespace Phprest\ErrorHandler\Formatter;

use Phprest\Config;
use Phprest\Application;
use Phprest\Service;
use Phprest\Entity;
use League\BooBoo\Formatter\AbstractFormatter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonXml extends AbstractFormatter
{
    use Service\Hateoas\Getter, Service\Hateoas\Util;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var null|Request
     */
    protected $request;

    /**
     * @param Config $config
     * @param null|Request $request
     */
    public function __construct(Config $config, Request $request = null)
    {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * @param \Exception $exception
     *
     * @return string
     */
    public function format(\Exception $exception)
    {
        $response = new Response();

        try {
            $response = $this->serialize(
                $this->config->isDebug() ? new Entity\DebugError($exception) : new Entity\Error($exception),
                is_null($this->request) ? Request::createFromGlobals() : $this->request,
                $response
            );
        } catch (\Exception $e) {
            $response->setContent(
                $this->serviceHateoas()->getSerializer()->serialize(
                    $this->config->isDebug() ? new Entity\DebugError($e) : new Entity\Error($e),
                    'json'
                )
            );

            $vendor = $this->getContainer()->get(Application::CNTRID_VENDOR);
            $apiVersion = $this->getContainer()->get(Application::CNTRID_API_VERSION);

            $response->headers->set('Content-Type', 'application/vnd.' . $vendor . '-v' . $apiVersion . '+json');
        }

        $response->setStatusCode(method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);

        $response->sendHeaders();

        return $response->getContent();
    }

    /**
     * @return \League\Container\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->config->getContainer();
    }
}

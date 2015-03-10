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
     * @param $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Exception $exception
     */
    public function format(\Exception $exception)
    {
        $this->handleCli($exception);

        $response = new Response();

        try {
            $response = $this->serialize(
                $this->config->isDebug() ? new Entity\DebugError($exception) : new Entity\Error($exception),
                Request::createFromGlobals(),
                $response
            );
        } catch (\Exception $e) {
            $response->setContent(
                $this->serviceHateoas()->getSerializer()->serialize(
                    $this->config->isDebug() ? new Entity\DebugError($exception) : new Entity\Error($exception),
                    'json'
                )
            );

            $vendor = $this->getContainer()->get(Application::CNTRID_VENDOR);
            $apiVersion = $this->getContainer()->get(Application::CNTRID_API_VERSION);

            $response->headers->set('Content-Type', 'application/vnd.' . $vendor . '-v' . $apiVersion . '+json');
        }

        $response->setStatusCode(method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);

        $response->send();
    }

    /**
     * @param \Exception $exception
     *
     * @throws \Exception
     */
    protected function handleCli(\Exception $exception)
    {
        if (php_sapi_name() === 'cli') {
            throw $exception;
        }
    }

    /**
     * @return \League\Container\Container
     */
    protected function getContainer()
    {
        return $this->config->getContainer();
    }
}

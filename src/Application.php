<?php namespace Phprest;

use Phprest\Router\RouteCollection;
use Phprest\Service;
use Phprest\Entity;
use Phprest\ErrorHandler\Handler\Log as LogHandler;
use League\Container\Exception\ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;

class Application extends \Proton\Application
{
    const CNTRID_DEBUG = 'debug';
    const CNTRID_VENDOR = 'vendor';
    const CNTRID_API_VERSION = 'api-version';
    const CNTRID_ROUTER = 'router';

    const API_VERSION_REG_EXP = '((?:[0-9](?:\.[0-9])?){1})';

    use Service\Hateoas\Getter, Service\Hateoas\Util;
    use Service\Logger\Getter;

    /**
     * @var Config
     */
    protected $configuration;

    /**
     * @var \League\Container\Container
     */
    protected $container;

    /**
     * @param Config $configuration
     */
    public function __construct(Config $configuration)
    {
        $this->configuration = $configuration;
        $this->container = $configuration->getContainer();
        $this->router = $configuration->getRouter();
        $this->eventEmitter = $configuration->getEventEmitter();

        AnnotationRegistry::registerLoader('class_exists');

        $this->registerService($configuration->getHateoasService(), $configuration->getHateoasConfig());
        $this->registerService($configuration->getLoggerService(), $configuration->getLoggerConfig());

        $this->setErrorHandler();

        $this->container->add(self::CNTRID_VENDOR, $configuration->getVendor());
        $this->container->add(self::CNTRID_API_VERSION, $configuration->getApiVersion());
        $this->container->add(self::CNTRID_DEBUG, $configuration->isDebug());
        $this->container->add(self::CNTRID_ROUTER, function() { return $this->router; } );
    }

    /**
     * @param Service\Serviceable $service
     * @param Service\Configurable $config
     *
     * @return void
     */
    public function registerService(Service\Serviceable $service, Service\Configurable $config)
    {
        $service->register($this->container, $config);
    }

    /**
     * @param string $class Namespaced class name
     *
     * @return void
     */
    public function registerController($class)
    {
        $controller = new $class($this->container);

        $this->container->add($class, function () use ($controller) {
            return $controller;
        });
    }

    /**
     * Run the application
     *
     * @param Request $request
     *
     * @return string
     */
    public function run(Request $request = null)
    {
        $app = (new \Stack\Builder())
            ->push('Phprest\Middleware\ApiVersion')
            ->resolve($this);

        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        $response = $app->handle($request);
        $response->send();

        $app->terminate($request, $response);
    }

    /**
     * Add a HEAD route
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function head($route, $action)
    {
        $this->router->addRoute('HEAD', $route, $action);
    }

    /**
     * Add a OPTIONS route
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function options($route, $action)
    {
        $this->router->addRoute('OPTIONS', $route, $action);
    }

    /**
     * @return Config
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return \League\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return RouteCollection
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return void
     */
    protected function setErrorHandler()
    {
        $this->setExceptionDecorator(function (\Exception $e) {
            throw $e;
        });

        $this->configuration->getErrorHandler()->pushHandler(new LogHandler($this->serviceLogger()));
        $this->configuration->getErrorHandler()->register();
    }
}

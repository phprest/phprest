<?php namespace Phprest;

use Stack;
use Phprest\Router\RouteCollection;
use Phprest\Service;
use Phprest\Entity;
use League\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Response;

class Application extends \Proton\Application
{
    const CNTRID_DEBUG          = 'debug';
    const CNTRID_VENDOR         = 'vendor';
    const CNTRID_API_VERSION    = 'api-version';
    const CNTRID_ROUTER         = 'router';

    const API_VERSION_REG_EXP   = '((?:[0-9](?:\.[0-9])?){1})';

    use Service\Hateoas\Getter, Service\Hateoas\Util;
    use Service\Logger\Getter;

    /**
     * @var Config
     */
    protected $configuration;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Stack\Builder
     */
    protected $stackBuilder;

    /**
     * @param Config $configuration
     */
    public function __construct(Config $configuration)
    {
        $this->configuration    = $configuration;
        $this->container        = $configuration->getContainer();
        $this->router           = $configuration->getRouter();
        $this->emitter          = $configuration->getEventEmitter();

        AnnotationRegistry::registerLoader('class_exists');

        $this->registerService($configuration->getHateoasService(), $configuration->getHateoasConfig());
        $this->registerService($configuration->getLoggerService(), $configuration->getLoggerConfig());

        $this->setErrorHandler();

        $this->container->add(self::CNTRID_VENDOR, $configuration->getVendor());
        $this->container->add(self::CNTRID_API_VERSION, $configuration->getApiVersion());
        $this->container->add(self::CNTRID_DEBUG, $configuration->isDebug());
        $this->container->add(self::CNTRID_ROUTER, function () {
            return $this->router;
        });

        $this->stackBuilder = new Stack\Builder;
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
     * @param string $classPath
     * @param array $arguments
     */
    public function registerMiddleware($classPath, array $arguments = [])
    {
        call_user_func_array([$this->stackBuilder, 'push'], array_merge([$classPath], $arguments));
    }

    /**
     * Run the application
     *
     * @param Request $request
     * @param array $middleWares
     *
     * @return string
     */
    public function run(Request $request = null, array $middleWares = [])
    {
        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        $this->registerMiddleware('Phprest\Middleware\ApiVersion');

        $app = $this->stackBuilder->resolve($this);

        $response = $app->handle($request, self::MASTER_REQUEST, false);
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
     * @return ContainerInterface
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
        $app = $this;

        $this->configuration->getLogHandler()->setLogger($this->serviceLogger());

        $this->configuration->getErrorHandler()->pushHandler($this->configuration->getLogHandler());
        $this->configuration->getErrorHandler()->register();

        $this->setExceptionDecorator(function (\Exception $e) use ($app) {
            $formatter = new ErrorHandler\Formatter\JsonXml($app->configuration);

            return new Response($formatter->format($e), http_response_code());
        });
    }
}

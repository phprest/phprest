<?php namespace Phprest;

use Exception;
use LogicException;
use Stack;
use Phprest\Service;
use League\Event\EmitterTrait;
use Phprest\Router\RouteCollection;
use League\Container\ContainerAwareTrait;
use League\Event\ListenerAcceptorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Phprest\Middleware\ApiVersion;

class Application implements
    HttpKernelInterface,
    TerminableInterface,
    ListenerAcceptorInterface
{
    use EmitterTrait;
    use ContainerAwareTrait;

    const CONTAINER_ID_DEBUG = 'debug';
    const CONTAINER_ID_VENDOR = 'vendor';
    const CONTAINER_ID_API_VERSION = 'api-version';
    const CONTAINER_ID_ROUTER = 'router';

    const API_VERSION_REG_EXP = '((?:[0-9](?:\.[0-9])?){1})';

    use Service\Hateoas\Getter, Service\Hateoas\Util;
    use Service\Logger\Getter;

    /**
     * @var Config
     */
    protected $configuration;

    /**
     * @var Stack\Builder
     */
    protected $stackBuilder;

    /**
     * @var \League\Route\RouteCollection
     */
    protected $router;

    /**
     * @var callable
     */
    protected $exceptionDecorator;

    /**
     * @param Config $configuration
     */
    public function __construct(Config $configuration)
    {
        $this->configuration = $configuration;
        $this->container = $configuration->getContainer();
        $this->router = $configuration->getRouter();
        $this->emitter = $configuration->getEventEmitter();

        AnnotationRegistry::registerLoader('class_exists');

        $this->registerService($configuration->getHateoasService(), $configuration->getHateoasConfig());
        $this->registerService($configuration->getLoggerService(), $configuration->getLoggerConfig());

        $this->setErrorHandler();

        $this->container->add(self::CONTAINER_ID_VENDOR, $configuration->getVendor());
        $this->container->add(self::CONTAINER_ID_API_VERSION, $configuration->getApiVersion());
        $this->container->add(self::CONTAINER_ID_DEBUG, $configuration->isDebug());
        $this->container->add(self::CONTAINER_ID_ROUTER, function () {
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
     *
     * @return string
     */
    public function run(Request $request = null)
    {
        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        $this->registerMiddleware(ApiVersion::class);

        $app = $this->stackBuilder->resolve($this);

        $response = $app->handle($request, self::MASTER_REQUEST, false);
        $response->send();

        $app->terminate($request, $response);
    }

    /**
     * Handle the request.
     *
     * @param Request $request
     * @param int $type
     * @param bool $catch
     *
     * @return Response
     * @throws LogicException
     *
     * @throws Exception
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        // Passes the request to the container
        $this->getContainer()->add(Request::class, $request);

        try {
            $this->emit('request.received', $request);

            $dispatcher = $this->getRouter()->getDispatcher();
            $response = $dispatcher->dispatch(
                $request->getMethod(),
                $request->getPathInfo()
            );

            $this->emit('response.created', $request, $response);

            return $response;
        } catch (Exception $e) {
            if (!$catch) {
                throw $e;
            }

            $response = call_user_func($this->exceptionDecorator, $e);
            if (!$response instanceof Response) {
                throw new LogicException(
                    'Exception decorator did not return an instance of Symfony\Component\HttpFoundation\Response'
                );
            }

            $this->emit('response.created', $request, $response);

            return $response;
        }
    }

    /**
     * Add a HEAD route
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function head($route, $action): void
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
    public function options($route, $action): void
    {
        $this->router->addRoute('OPTIONS', $route, $action);
    }

    /**
     * Add a GET route.
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function get($route, $action): void
    {
        $this->getRouter()->addRoute('GET', $route, $action);
    }

    /**
     * Add a POST route.
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function post($route, $action): void
    {
        $this->getRouter()->addRoute('POST', $route, $action);
    }

    /**
     * Add a PUT route.
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function put($route, $action)
    {
        $this->getRouter()->addRoute('PUT', $route, $action);
    }

    /**
     * Add a DELETE route.
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function delete($route, $action): void
    {
        $this->getRouter()->addRoute('DELETE', $route, $action);
    }

    /**
     * Add a PATCH route.
     *
     * @param string $route
     * @param mixed $action
     *
     * @return void
     */
    public function patch($route, $action): void
    {
        $this->getRouter()->addRoute('PATCH', $route, $action);
    }

    /**
     * @return Config
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return RouteCollection
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Return the event emitter.
     *
     * @return \League\Event\EmitterInterface
     */
    public function getEventEmitter()
    {
        return $this->getEmitter();
    }

    /**
     * Terminates a request/response cycle.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        $this->emit('response.sent', $request, $response);
    }

    /**
     * Subscribe to an event.
     *
     * @param string $event
     * @param callable $listener
     * @param int $priority
     */
    public function subscribe($event, $listener, $priority = ListenerAcceptorInterface::P_NORMAL)
    {
        $this->addListener($event, $listener, $priority);
    }

    /**
     * Set the exception decorator.
     *
     * @param callable $func
     *
     * @return void
     */
    public function setExceptionDecorator(callable $func)
    {
        $this->exceptionDecorator = $func;
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

        $this->setExceptionDecorator(function (Exception $e) use ($app) {
            $formatter = new ErrorHandler\Formatter\JsonXml($app->configuration);

            return new Response($formatter->format($e), http_response_code());
        });
    }
}

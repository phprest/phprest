<?php

namespace Phprest;

use Closure;
use Doctrine\Common\Annotations\AnnotationRegistry;
use League\Container\ContainerAwareTrait;
use League\Event\EmitterInterface;
use League\Event\EmitterTrait;
use League\Event\ListenerAcceptorInterface;
use Phprest\Middleware\ApiVersion;
use Phprest\Router\RouteCollection;
use Phprest\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use LogicException;
use Exception;
use Stack;

class Application implements
    HttpKernelInterface,
    TerminableInterface,
    ListenerAcceptorInterface
{
    use EmitterTrait;
    use ContainerAwareTrait;
    use Service\Hateoas\Getter;
    use Service\Hateoas\Util;
    use Service\Logger\Getter;

    public const CONTAINER_ID_DEBUG = 'debug';
    public const CONTAINER_ID_VENDOR = 'vendor';
    public const CONTAINER_ID_API_VERSION = 'api-version';
    public const CONTAINER_ID_ROUTER = 'router';
    public const API_VERSION_REG_EXP = '((?:[0-9](?:\.[0-9])?){1})';

    protected Config $configuration;
    protected Stack\Builder $stackBuilder;
    protected RouteCollection $router;

    /**
     * @var callable
     */
    protected $exceptionDecorator;

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

        $this->stackBuilder = new Stack\Builder();
    }

    public function registerService(Service\Serviceable $service, Service\Configurable $config): void
    {
        $service->register($this->container, $config);
    }

    /**
     * @param string $class Namespaced class name
     */
    public function registerController(string $class): void
    {
        $controller = new $class($this->container);

        $this->container->add($class, static function () use ($controller) {
            return $controller;
        });
    }

    public function registerMiddleware(string $classPath, array $arguments = []): void
    {
        call_user_func_array([$this->stackBuilder, 'push'], array_merge([$classPath], $arguments));
    }

    /**
     * Run the application
     */
    public function run(Request $request = null): void
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
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true): ?Response
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
     * @param string|Closure $action
     */
    public function head(string $route, $action): self
    {
        $this->router->addRoute('HEAD', $route, $action);

        return $this;
    }

    /**
     * Add a OPTIONS route
     *
     * @param string|Closure $action
     */
    public function options(string $route, $action): self
    {
        $this->router->addRoute('OPTIONS', $route, $action);

        return $this;
    }

    /**
     * Add a GET route.
     *
     * @param string|Closure $action
     */
    public function get(string $route, $action): self
    {
        $this->getRouter()->addRoute('GET', $route, $action);

        return $this;
    }

    /**
     * Add a POST route.
     *
     * @param string|Closure $action
     */
    public function post(string $route, $action): self
    {
        $this->getRouter()->addRoute('POST', $route, $action);

        return $this;
    }

    /**
     * Add a PUT route.
     *
     * @param string|Closure $action
     */
    public function put(string $route, $action): self
    {
        $this->getRouter()->addRoute('PUT', $route, $action);

        return $this;
    }

    /**
     * Add a DELETE route.
     *
     * @param string|Closure $action
     */
    public function delete(string $route, $action): self
    {
        $this->getRouter()->addRoute('DELETE', $route, $action);

        return $this;
    }

    /**
     * Add a PATCH route.
     *
     * @param string|Closure $action
     */
    public function patch(string $route, $action): self
    {
        $this->getRouter()->addRoute('PATCH', $route, $action);

        return $this;
    }

    public function getConfiguration(): Config
    {
        return $this->configuration;
    }

    public function getRouter(): RouteCollection
    {
        return $this->router;
    }

    /**
     * Return the event emitter.
     */
    public function getEventEmitter(): EmitterInterface
    {
        return $this->getEmitter();
    }

    /**
     * Terminates a request/response cycle.
     */
    public function terminate(Request $request, Response $response): void
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
     */
    public function setExceptionDecorator(callable $func): void
    {
        $this->exceptionDecorator = $func;
    }

    protected function setErrorHandler(): void
    {
        $app = $this;

        $this->configuration->getLogHandler()->setLogger($this->serviceLogger());

        $this->configuration->getErrorHandler()->pushHandler($this->configuration->getLogHandler());
        $this->configuration->getErrorHandler()->register();

        $this->setExceptionDecorator(static function (Exception $e) use ($app) {
            $formatter = new ErrorHandler\Formatter\JsonXml($app->configuration);

            return new Response($formatter->format($e), http_response_code());
        });
    }
}

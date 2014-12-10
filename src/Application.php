<?php namespace Phprest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Phprest\Service\Hateoas\Config as HateoasConfig;
use Phprest\Service;
use Phprest\Router\Strategy;
use Phprest\Entity;

class Application extends \Proton\Application
{
    const CONFIG_DEBUG = 'debug';
    const CONFIG_VENDOR = 'vendor';
    const CONFIG_API_VERSION = 'api-version';
    const CONFIG_API_VERSION_HANDLER = 'api-version-handler';
    const CONFIG_ROUTER = 'router';

    use Service\Hateoas\Getter, Service\Hateoas\Util;

    /**
     * @var boolean
     */
    protected $debug = false;

    /**
     * @var callable
     */
    protected $exceptionHandler;

    /**
     * @var array
     */
    protected $registeredServiceNames = [];

    /**
     * @param string $vendor
     * @param int|string $apiVersion
     * @param boolean $debug
     * @param HateoasConfig $hateoasConfig
     * @param Strategy $routerStrategy
     */
    public function __construct($vendor,
                                $apiVersion,
                                $debug = false,
                                HateoasConfig $hateoasConfig = null,
                                Strategy $routerStrategy = null)
    {
        parent::__construct();

        $this->debug = $debug;

        $this->container->add(self::CONFIG_VENDOR, $vendor);
        $this->container->add(self::CONFIG_API_VERSION, $apiVersion);
        $this->container->add(self::CONFIG_DEBUG, $debug);
        $this->container->singleton(self::CONFIG_ROUTER, function() { return $this->router; } );

        $this->setErrorHandlers();
        $this->setRouterStrategy(is_null($routerStrategy) ? new Strategy($this->container) : $routerStrategy);
        $this->registerBuiltInServices($hateoasConfig);
    }

    /**
     * @param Service\Serviceable $service
     * @param Service\Configurable $config
     *
     * @return void
     *
     * @throws \Exception
     */
    public function registerService(Service\Serviceable $service, Service\Configurable $config)
    {
        if (in_array($config->getServiceName(), $this->registeredServiceNames)) {
            throw new \Exception('Service <' . $config->getServiceName() . '> has been already registered!');
        }

        $service->register($this->container, $config);

        $this->registeredServiceNames[] = $config->getServiceName();
    }

    /**
     * @param string $class Namespaced class name
     *
     * @return void
     */
    public function registerController($class)
    {
        $controller = new $class($this->container);

        $this->container->singleton($class, function () use ($controller) {
            return $controller;
        });
    }

    /**
     * @param Strategy $routerStrategy
     *
     * @return void
     */
    public function setRouterStrategy(Strategy $routerStrategy)
    {
        $this->router->setStrategy($routerStrategy);
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
     * @param callable $func
     *
     * @return void
     */
    public function setApiVersionHandler(callable $func)
    {
        $this->container->add(self::CONFIG_API_VERSION_HANDLER, function() use ($func) {
            return $func;
        });
    }

    /**
     * @param callable $func
     *
     * @return void
     */
    public function setDefaultExceptionHandler(callable $func) {
        set_exception_handler($func);

        $this->exceptionHandler = $func;
    }

    /**
     * @param HateoasConfig $hateoasConfig
     *
     * @return void
     */
    protected function registerBuiltInServices(HateoasConfig $hateoasConfig = null)
    {
        AnnotationRegistry::registerLoader('class_exists');

        if (is_null($hateoasConfig)) {
            $hateoasConfig = new HateoasConfig($this->debug);
        }

        $this->registerService(new Service\Hateoas\Service(), $hateoasConfig);
    }

    /**
     * @return void
     */
    protected function setErrorHandlers()
    {
        $this->setExceptionDecorator(function (\Exception $e) {
            throw $e;
        });

        set_error_handler(function($errNo, $errStr, $errFile, $errLine) {
            throw new \ErrorException($errStr, PHP_INT_MAX - 1, $errNo, $errFile, $errLine);
        });

        $this->setDefaultExceptionHandler(function(\Exception $exception) {
            $this->getExceptionResponse($exception)->send();
        });

        register_shutdown_function(function() {
            if ($error = error_get_last()) {
                call_user_func(
                    $this->exceptionHandler,
                    new \ErrorException($error['message'], PHP_INT_MAX, $error['type'], $error['file'], $error['line'])
                );
            }
        });
    }

    /**
     * Returns with a xml/json response.
     * Default: json.
     *
     * @param \Exception $exception
     *
     * @return Response
     */
    protected function getExceptionResponse(\Exception $exception)
    {
        $response = new Response();

        try {
            $response = $this->serialize(
                $this[self::CONFIG_DEBUG] === false ? new Entity\Error($exception) : new Entity\DebugError($exception),
                Request::createFromGlobals(),
                $response
            );
        } catch (\Exception $e) {
            $response->setContent(
                $this->serviceHateoas()->getSerializer()->serialize(
                    $this[self::CONFIG_DEBUG] === false ? new Entity\Error($exception) : new Entity\DebugError($exception),
                    'json'
                )
            );

            $vendor = $this->container->get(self::CONFIG_VENDOR);
            $apiVersion = $this->container->get(self::CONFIG_API_VERSION);

            $response->headers->set('Content-Type', 'application/vnd.' . $vendor . '-v' . $apiVersion . '+json');
        }

        $response->setStatusCode(method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);

        return $response;
    }
}
